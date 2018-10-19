<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 15.06.2017
 * Time: 16:47
 */

namespace StoreBundle\Controller\Catalog;

use AccurateCommerce\DataAdapter\ClientApplicationModelCollection;
use AccurateCommerce\Exception\SearchException;
use AccurateCommerce\Pagination\Pagination;
use AccurateCommerce\Search\CatalogSectionSearch;
use AccurateCommerce\Search\History\SearchHistory;
use AccurateCommerce\Search\ProductSearch;
use AccurateCommerce\Sort\ProductSort;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Accurateweb\TaxonomyBundle\Model\Taxon\SearchTaxon;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationSearch;
use Doctrine\Common\Collections\ArrayCollection;
use StoreBundle\DataAdapter\FilterFieldSchemaAdapter;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
  /**
   * Контроллер страницы поиска по каталогу
   *
   * @param Request $request
   * @return Response
   */
  public function searchAction(Request $request)
  {
    $query = $request->get('q');

    if (!$query)
    {
     throw $this->createNotFoundException('Search query was empty');
    }

    /** @var SearchTaxon $taxon */
    $taxon = $this->get('aw.search.taxon_factory')->getTaxon($query);
    /** @var TaxonPresentationSearch $presentation */
    $presentation = $this->get('aw.taxon_presentation.manager')->getTaxonPresentation($taxon);

    if (!$presentation->getNbProducts() && !$presentation->getNbTaxons())
    {
      return $this->render('@Store/Catalog/Search/index_empty.html.twig', [
        'query' => $query
      ]);
    }
    elseif($presentation->getNbProducts() === 1 && !$presentation->getNbTaxons())
    {
      $products = $presentation->getProducts();
      $product = $products[0];
      $this->redirectToRoute('product', ['slug' => $product->getSlug()]);
    }
    elseif(!$presentation->getNbProducts() && $presentation->getNbTaxons() === 1)
    {
      $taxons = $presentation->getTaxons();
      $taxon = $taxons[0];
      $this->redirectToRoute('taxon', ['slug' => $taxon->getSlug()]);
    }

    $presentation->prepare();

    return $this->render('StoreBundle:Catalog/Search:index.html.twig', [
      'presentation' => $presentation,
      'query' => $query,
    ]);

    $productFilter = null;
    $productFilterAdapter = null;

    $products = new ArrayCollection();
    $productClientModels = new ClientApplicationModelCollection();

    $pagination = null;
    $sort = null;

    if ($taxon->getProductCount())
    {
      $productQueryBuilder = $taxon->getProductQueryBuilder();

      $productFilter = new ProductFilter(null, $taxon);
      $productFilter->apply();

      $productQueryBuilder
        ->addOrderBy('p.isPurchasable', 'desc')
        ->addOrderBy("relevanceseq")
        ;

      $sort = new ProductSort(
        $request->query->get('column'),
        $request->query->get('order')
      );

      $pagination = new Pagination($productQueryBuilder, $request->query->get('page', 1), 24);

      $products = array_chunk($pagination->getIterator()->getArrayCopy(), 4);

      $router = $this->get('router');
      $productFilterAdapter = new FilterFieldSchemaAdapter($productFilter, $router, $pagination, $sort);

      foreach ($products as $chunk)
      {
        foreach ($chunk as $product)
        {
          $productClientModels->append($this->get('store.factory.product_client_adapter')->getModelAdapter($product));
        }
      }
    }


    return $this->render('StoreBundle:Catalog/Search:index.html.twig', [
      'taxon' => $taxon,
      'query' => $query,
      'children' => array_chunk($foundCatalogSections->toArray(), 4),
      'products' => $products,
      'productClientModels' => $productClientModels,
      'filter' => $productFilter,
      'filterClientAdapter' => $productFilterAdapter,
      'pagination' => $pagination,
      'sort' => $sort
    ]);
  }

  /*
   * Контроллер suggest для поиска по каталогу
   */
  public function suggestAction(Request $request)
  {
    $query = $request->get('term');
    if (!$query)
    {
      throw $this->createNotFoundException('Query is empty');
    }

    $suggestions = [];
    if (strlen(trim($query)) <= 2)
    {
      return new Response('Query too short (3 symbols min)', 400);
    }

    $router = $this->get('router');

    $sid = $request->get('sid');
    if ($sid == 'all')
    {
      $sid = null;
    }

    if ($sid)
    {
      $catalogSectionToSearchIn = $this->getDoctrine()
        ->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon')
        ->find($sid);

      if (!$catalogSectionToSearchIn)
      {
        throw $this->createNotFoundException(sprintf('Catalog section "%s" not found', $sid));
      }
    }
    else
    {
      $catalogSectionToSearchIn = null;
    }

    $productSearch = ProductSearch::create(
      $this->get('accurateweb.sphinxsearch')->getSphinxClient(),
      $query
    )
      ->setSortByRelevance(true)
      ->setCatalogSection($catalogSectionToSearchIn)
      ->setLimit(10);

//    if ($this->getUser()->hasCredential('admin'))
//    {
//      $productSearch->setSearchArchiveProducts(true);
//    }

    try
    {
      $products = $productSearch->execute()
        ->getObjects($this->getDoctrine()
          ->getRepository('StoreBundle:Store\Catalog\Product\Product')
          ->createQueryBuilder('p')
        );
    }
    catch (SearchException $e)
    {
      $products = new ArrayCollection();
    }


    $sections = CatalogSectionSearch::create(
        $this->get('accurateweb.sphinxsearch')->getSphinxClient(),
        $query
      )
      ->setCatalogSection($catalogSectionToSearchIn)
      ->setLimit(10)
      ->execute()
      ->getObjects($this->getDoctrine()
        ->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon')
        ->createQueryBuilder('t')
      );


    $sectionsNb = $sections->count();
    $productsNb = $products->count();

    $nbResultsTotal = $sectionsNb + $productsNb;

    $qs = [
      'from' => 'suggest',
      'sid' => SearchHistory::registerSearch($query, $nbResultsTotal)
    ];

    if ($sectionsNb)
    {
      $taxonRouteBuilder = $this->get('store.taxonomy.route.builder');

      $k = 0 ; $c = $productsNb >= 10 ? 3 : (10 - $productsNb) + 3;
      foreach ($sections as $object)
      {
        //if ($object instanceof CatalogSection && $object->getProductCount(null, true) > 0) {
          $k++;
          $suggestions[] = array(
            'label' => $this->renderView('@Store/Catalog/Search/Suggest/taxon.html.twig', array(
              'object' => $object,
              'url' => $taxonRouteBuilder->generate($object, null, $qs))
            ),
            'value' => $object->getName(),
            'url' => $taxonRouteBuilder->generate($object, null, $qs),
            'type' => 'taxon'
          );
        //}
        if ($k >= $c) break;
      }
    }

    if ($sectionsNb && $productsNb)
    {
      $suggestions[] = array(
        'label' => $this->renderView('@Store/Catalog/Search/Suggest/separator.html.twig'),
        'value' => '', 'url' => '', 'type' => 'separate');
    }

    if ($productsNb)
    {
      $k = 0; $p = $sectionsNb >= 3 ? 7 : (10 - $sectionsNb);
      foreach ($products as $object)
      {
        if ($object instanceof Product)
        {
          $k++;
          $suggestions[] = array(
            'label' => $this->renderView('@Store/Catalog/Search/Suggest/product.html.twig', array(
              'search' => $productSearch,
              'object' => $object,
              'url' => $router->generate('product', array_merge($qs, ['slug' => $object->getSlug()])),
              'query' => $query
            )),
            'value' => $object->getName(),
            'url' => $router->generate('product', array_merge($qs, ['slug' => $object->getSlug()])),
            'type' => 'products'
          );
        }
        if ($k >= $p) break;
      }
    }

    if ($nbResultsTotal > 7)
    {
      $suggestions[] = array('label' => $this->renderView('@Store/Catalog/Search/Suggest/separator.html.twig'), 'value' => '', 'url' => '', 'type' => 'separate');
      $suggestions[] = array(
        'label' => $this->renderView('@Store/Catalog/Search/Suggest/allresults.html.twig', array(
          'query' => $query)
        ),
        'value' => 'Все результаты',
        'url' => $router->generate('catalog_search', ['q' => stripcslashes($query)]), 
        'type' => 'all-results'
      );
    }

    if (empty($suggestions))
    {
      $suggestions[] = array('label' => '', 'value' => $query, 'url' => '', 'type' => 'no-result');
    }

    return new JsonResponse($suggestions);
  }
}