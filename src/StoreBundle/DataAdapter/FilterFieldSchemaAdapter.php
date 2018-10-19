<?php

namespace StoreBundle\DataAdapter;

use AccurateCommerce\DataAdapter\ClientApplicationModelAdapterInterface;
use AccurateCommerce\Pagination\Pagination;
use AccurateCommerce\Sort\ProductSort;
use AccurateCommerce\Store\Catalog\Filter\BaseFilter;
use Sonata\AdminBundle\Filter\Filter;
use Symfony\Component\Routing\Router;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class FilterFieldSchemaAdapter implements ClientApplicationModelAdapterInterface
{
  const NB_LINKS = 5;

  private $filter;

  private $router;

  private $pagination;

  private $sort;

  public function __construct(BaseFilter $filter, Router $router, Pagination $pagination, ProductSort $sort)
  {
    $this->filter = $filter;
    $this->router = $router;
    $this->pagination = $pagination;
    $this->sort = $sort;
  }

  function getClientModelName()
  {
    return 'Filter';
  }

  function getClientModelValues($context = null)
  {
    $taxon = $this->filter->getTaxon();

    return array(
      'schema' => $this->getSchemaAsArray(),
      'state' => $this->getFilterState(),
      'section' => array(
        'url' => $this->router->generate($taxon->getRouteName(), $taxon->getUrlParameters())
      ),
      "pagination" => array(
        "pages" => array(
          'last' => $this->pagination->getLastPage(),
          'current' => $this->pagination->getPage()
        ),
        'per_page' => $this->pagination->getMaxPerPage(),
        'nbresults' => $this->pagination->getNbResults(),
        'available_per_page' => array(),
        "links" => $this->getPaginationLinks(),
        'nb_links' => self::NB_LINKS
      ),
      'defaults' => array(
        'count' => 24,
        'column' => null,
        'order' => null,
        'view' => 'grid'
      ),
      "sort" => array(
        'column' => $this->sort->getColumn(),
        'order' => $this->sort->getOrder(),
        'next' => $this->sort->getOrder() == 'asc' ? 'desc' : 'asc'
      ),
      'sortColumns' => array(
        'rank' => 'по популярности',
        'price' => 'по цене'
      )
    );
  }

  function getClientModelId()
  {
    return 'Filter';
  }

  public function getSchemaAsArray($schema=null, $parentKey = null)
  {
    if (null === $schema)
    {
      return $this->getSchemaAsArray($this->filter->getFields());
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

  public function getFilterState($parentKey = null)
  {
    $states = array();

    foreach ($this->filter->getFields() as $key => $field)
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

  private function getPaginationLinks()
  {
    $links = array();
    
    $tmp	 = $this->pagination->getPage() - floor(self::NB_LINKS / 2);
    $check = $this->pagination->getLastPage() - self::NB_LINKS + 1;
    $limit = ($check > 0) ? $check : 1;
    $begin = ($tmp > 0) ? (($tmp > $limit) ? $limit : $tmp) : 1;

    $i = (int) $begin;
    while (($i < $begin + self::NB_LINKS) && ($i <= $this->pagination->getLastPage()))
    {
      $links[] = $i++;
    }

    return $links;
  }
}