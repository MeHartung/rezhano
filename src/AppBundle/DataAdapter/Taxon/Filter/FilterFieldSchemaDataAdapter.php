<?php

namespace AppBundle\DataAdapter\Taxon\Filter;


use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Pagination\Pagination;
use AccurateCommerce\Sort\ProductSort;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonFilterableInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPaginationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonSortableInterface;
use Symfony\Component\Routing\RouterInterface;

class FilterFieldSchemaDataAdapter implements ClientApplicationModelAdapterInterface
{
  const NB_LINKS = 5;
  
  private $router;
  
  public function __construct (RouterInterface $router)
  {
    $this->router = $router;
  }

  /**
   * @param $subject TaxonPresentationInterface
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    /** @var TaxonInterface $taxon */
    $taxon = $subject->getTaxon();
    
    if ($subject instanceof TaxonPaginationInterface)
    {
      $pagination = $subject->getPagination();
    }
    else
    {
      $pagination = new Pagination($taxon->getProductQueryBuilder(), 1, 10);
    }
    
    if ($subject instanceof TaxonSortableInterface)
    {
      $sort = $subject->getSort();
    }
    else
    {
      $sort = new ProductSort('price', 'asc');
    }

    $data = array(
      'section' => array(
        'url' => $this->router->generate($taxon->getRouteName(), $taxon->getUrlParameters())
      ),
      "pagination" => array(
        "pages" => array(
          'last' => $pagination->getLastPage(),
          'current' => $pagination->getPage()
        ),
        'per_page' => $pagination->getMaxPerPage(),
        'nbresults' => $pagination->getNbResults(),
        'available_per_page' => array(),
        "links" => $this->getPaginationLinks($pagination),
        'nb_links' => self::NB_LINKS
      ),
      'defaults' => array(
        'count' => 24,
        'column' => null,
        'order' => null,
        'view' => 'grid'
      ),
      "sort" => array(
        'column' => $sort->getColumn(),
        'order' => $sort->getOrder(),
        'next' => $sort->getOrder() == 'asc' ? 'desc' : 'asc',
        'icrf' => $sort->isDisplayOffersInCustomerRegionFirst()
      ),
      'sortColumns' => array(
        'rank' => 'по популярности',
        'price' => 'по цене'
      )
    );

    if ($subject instanceof TaxonFilterableInterface)
    {
      $data['schema'] = $this->getSchemaAsArray($subject->getFilter());
      $data['state'] = $this->getFilterState($subject->getFilter());
    }

    return $data;
  }

  public function getModelName ()
  {
    return 'Filter';
  }

  public function supports ($subject)
  {
    return $subject instanceof TaxonPresentationInterface;
  }

  private function getPaginationLinks(Pagination $pagination)
  {
    $links = array();

    $tmp	 = $pagination->getPage() - floor(self::NB_LINKS / 2);
    $check = $pagination->getLastPage() - self::NB_LINKS + 1;
    $limit = ($check > 0) ? $check : 1;
    $begin = ($tmp > 0) ? (($tmp > $limit) ? $limit : $tmp) : 1;

    $i = (int) $begin;

    while (($i < $begin + self::NB_LINKS) && ($i <= $pagination->getLastPage()))
    {
      $links[] = $i++;
    }

    return $links;
  }

  public function getSchemaAsArray(ProductFilter $filter, $schema=null, $parentKey = null)
  {
    if (null === $schema)
    {
      return $this->getSchemaAsArray($filter, $filter->getFields());
    }

    $result = array();

    foreach ($schema as $key => $field)
    {
      $filterName = is_null($parentKey) ? $key : $parentKey . "_" . $key ;

      //      if ($field instanceof asBaseFilterFieldSchema)
      //      {
      //        $result = array_merge($result, $this->getSchemaAsArray($field, $filterName));
      //      }
      //      else
      {
        $result[$filterName] = $field->getSchema();
      }
    }

    return $result;
  }

  public function getFilterState(ProductFilter $filter, $parentKey = null)
  {
    $states = array();

    foreach ($filter->getFields() as $key => $field)
    {
      $filterName = is_null($parentKey) ? $key : $parentKey . "_" . $key ;

      $state = $field->getState();

      if (null !== $state)
      {
        $states[$filterName] = $state;
      }
    }

    return $states;
  }
}