<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Controller\Catalog;

use AccurateCommerce\DataAdapter\ClientApplicationModelCollection;
use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Sort\ProductSort;
use AccurateCommerce\Pagination\Pagination;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException;
use Accurateweb\TaxonomyBundle\Exception\TaxonPresentationNotFoundException;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationChildSections;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationProducts;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonFilterableInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPaginationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonSortableInterface;
use Doctrine\ORM\NoResultException;
use StoreBundle\DataAdapter\FilterFieldSchemaAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaxonomyController extends Controller
{
  /**
   * Контроллер главной страницы каталога.
   */
  public function indexAction()
  {
    try
    {
      $taxon = $this->get('aw.taxonomy.manager')->getTaxon('root');
    }
    catch (TaxonNotFoundException $e)
    {
      throw $this->createNotFoundException('Root taxon not found');
    }

    $presentation = new TaxonPresentationChildSections($taxon);
    $presentation->prepare();

    return $this->render('@Store/Catalog/Taxon/show.html.twig', [
      'taxon' => $taxon,
      'presentation' => $presentation,
    ]);
  }

  /**
   * Контроллер страницы раздела каталога
   *
   * @param Request $request
   * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function taxonAction (Request $request)
  {
    $slug = $request->get("slug");

    /** @var TaxonInterface $taxon */
    try
    {
      $taxon = $this->get("aw.taxonomy.manager")->getTaxon($slug);
    }
    catch (TaxonNotFoundException $exception)
    {
      throw $this->createNotFoundException(sprintf('Категория %s не найдена', $request->get('slug')));
    }

    $presentationOptions = array(
      'sort_column' => $request->query->get('column', 'rank'),
      'sort_order' => $request->query->get('order', 'asc'),
      'pagination_page' => $request->query->get('page', 1),
      'pagination_max_per_page' => $request->query->get('count', 15)
    );

    try
    {
      $presentation = $this->get('aw.taxon_presentation.manager')->getTaxonPresentation($taxon, $presentationOptions);
    }
    catch (TaxonPresentationNotFoundException $e)
    {
      //Будем по умолчанию использовать представление каталога со списком товаров
      $presentation = new TaxonPresentationProducts($taxon, $this->get('aw.product_sort.factory'));
    }

    if ($presentation instanceof TaxonFilterableInterface)
    {
      $productFilter = $presentation->getFilter();

      /*
       * Чтобы применить фильтра по каталогу, сначала необходимо создать форму фильтра и заполнить ее значениями
       */
      $productFilterForm = $productFilter->createForm($this->container->get('form.factory'), array(
        'attr' => array(
          'class' => 'mcf_form'
        )
      ));

      $filterValues = $request->get('f');

      if ($filterValues)
      {
        $productFilterForm->submit($filterValues);
        /*
         * Вопрос зыключается в том, что делать, если форма фильтра не валидна
         */
      }
    }

    # мы не пожем построить путь к виртуальному разделу
    $path = !$taxon->getId() ? [] : $this->getDoctrine()
      ->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon')
      ->getPath($taxon->getTaxonEntity());

    $presentation->prepare();

    if (!$request->isXmlHttpRequest())
    {
      return $this->render('StoreBundle:Catalog\Taxon:show.html.twig', array(
        'taxon' => $taxon,
        'presentation' => $presentation,
        'path' => $path,
        'filter' => isset($productFilter)?$productFilter:null,
        'filterForm' => isset($productFilterForm)?$productFilterForm->createView():null,
      ));
    }
    else
    {
      $response = new JsonResponse(array(
        'filter' => $this->get('aw.client_application.transformer')->getClientModelData($presentation, 'filter'),
        'products' => $this->get('aw.client_application.transformer')->getClientModelCollectionData($presentation->getProducts(), 'product'),
      ));

      $response->setPrivate();
      $response->setMaxAge(0);
      $response->setSharedMaxAge(0);

      $response->headers->addCacheControlDirective('must-revalidate', true);
      $response->headers->addCacheControlDirective('no-store', true);
      $response->headers->addCacheControlDirective('no-cache', true);

      $response->expire();

      return $response;
    }
  }

//  public function _productListItemAction(Product $product)
//  {
//    $image = $product->getMainImage();
//
//    return $this->render('StoreBundle:Catalog\Product:product_card.html.twig', array(
//      'product' => $product,
//      'image' => $image
//    ));
//  }

  public function _catalogHeaderMenuAction()
  {
    $repository = $this->getDoctrine()->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon');

    try
    {
      $root = $repository->getRootNode();
    }
    catch (NoResultException $e)
    {
      return new Response('');
    }

    $nodeQb = $repository->childrenQueryBuilder($root);
    $nodes = $nodeQb->andWhere('node.treeLevel < :maxTreeLevel')
                    ->setParameter('maxTreeLevel', 4)
                    ->getQuery()
                    ->getArrayResult();

    $tree = $repository->buildTreeArray($nodes);

    foreach ($tree as $key=>$item)
    {
      if ($item['treeLevel'] == 1)
      {
        $tree[$key]['name'] = $item['shortName'];
      }
    }

    return $this->render('StoreBundle:Catalog/Taxon:_headerMenu.html.twig', array(
      'nodes' => array_splice($tree, 0, 4),
      'nodes_rest' => $tree
    ));
  }
}