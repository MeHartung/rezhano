<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Pagination\Pagination;
use AccurateCommerce\Sort\ProductSort;
use AccurateCommerce\Sort\ProductSortFactoryInterface;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonFilterableInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPaginationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonSortableInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonPresentationProducts implements TaxonPresentationInterface, TaxonPaginationInterface, TaxonSortableInterface, TaxonFilterableInterface
{
  /**
   * @var TaxonInterface
   */
  private $taxon;

  /**
   * @var ProductSort
   */
  private $sort;

  /**
   * @var Pagination
   */
  private $pagination;

  /**
   * @var ProductFilter
   */
  private $filter;

  /**
   * @var array
   */
  private $parameters = [];

  /**
   * @var ProductSortFactoryInterface
   */
  private $productSortFactory;

  private $options;

  /**
   * Конструктор
   *
   * @param TaxonInterface $taxon Раздел каталога
   *
   * Доступные опции:
   * * pagination_page
   * * pagination_max_per_page
   * * sort_column
   * * sort_order
   * @param array $options Опции представления
   */
  public function __construct (TaxonInterface $taxon, ProductSortFactoryInterface $sortFactory, array $options = [])
  {
    $this->productSortFactory = $sortFactory;

    $this->setParameters($taxon->getPresentationOptions());

    $optionsResolver = new OptionsResolver();

    $this->configureOptions($optionsResolver);

    $this->options = $optionsResolver->resolve($options);

    $this->taxon = $taxon;
    $this->filter = new ProductFilter(null, $taxon);
  }

  protected function configureOptions(OptionsResolver $optionsResolver)
  {
    $optionsResolver->setDefaults([
      'pagination_page' => 1,
      'pagination_max_per_page' => 24,
      'sort_column' => 'price',
      'sort_order' => 'asc',
      'sort_in_customer_region_first' => false
    ]);

    $optionsResolver->addAllowedValues('sort_order', ['asc', 'desc', null]);
  }

  /**
   * Выполняет логику контроллера презентации.
   */
  public function prepare()
  {
    /*
     * Паджинация, сортировка и фильтр должны использовать один и тот же экземпляр QueryBuilder, поэтому
     * нам необходимо явно передать его всем участникам цепочки преобразования запроса на выборку товаров
     */
    $productQueryBuilder = $this->filter->apply();

    /*
     * @TODO:
     * Мы хотим применять сортировки после расчета паджинации, чтобы при расчете паджинации не было дополнительных операций.
     * Также сортировки могут быть сложными, использовать join'ы и группировки. Это все тоже не требуется при расчете паджинации
     */

    $this->sort = $this->productSortFactory->create($this->getSortOptions());
    $this->sort->apply($productQueryBuilder);

    $this->pagination = new Pagination($productQueryBuilder, $this->options['pagination_page'], $this->options['pagination_max_per_page']);



    /*
     * Явно переопределим значения параметров с тем, чтобы подсчитанные значения гарантированно соответствовали
     * настройкам представления.
     */
    $this->parameters['sort'] = $this->sort;
    $this->parameters['pagination'] = $this->pagination;
  }

  public function getTaxon ()
  {
    return $this->taxon;
  }

  public function getTemplateName ()
  {
    return '@Store/Catalog/Taxon/presentation/products.html.twig';
  }

  public function getPagination ()
  {
    return $this->pagination;
  }

  public function getSort ()
  {
    return $this->sort;
  }

  public function getParameters ()
  {
    return $this->parameters;
  }

  public function getFilter ()
  {
    return $this->filter;
  }

  public function setParameters ($parameters)
  {
    if (isset($parameters['sort']))
    {
      if ($parameters['sort'] instanceof ProductSort)
      {
        $this->sort = $parameters['sort'];
      }
      elseif (is_array($parameters['sort']))
      {
        $this->sort = new ProductSort(
          isset($parameters['sort']['column'])?$parameters['sort']['column']:'price',
          isset($parameters['sort']['order'])?$parameters['sort']['order']:'asc'
        );
      }
    }

    if(isset($parameters['pagination']) && $parameters['pagination'] instanceof Pagination)
    {
      $this->pagination = $parameters['pagination'];
    }

    if (isset($parameters['f']))
    {
      if ($parameters['f'] instanceof ProductFilter)
      {
        $this->filter = $parameters['f'];
      }
    }

    $this->parameters = $parameters;

    return $this;
  }

  public function addParameter ($param, $value)
  {
    if ($param === 'sort')
    {
      if ($value instanceof ProductSort)
      {
        $this->sort = $value;
      }
      elseif (is_array($value))
      {
        $this->sort = new ProductSort(
          isset($value['column'])?$value['column']:'price',
          isset($value['order'])?$value['order']:'asc'
        );
      }
    }

    if($param === 'pagination' && $value instanceof Pagination)
    {
      $this->pagination = $value;
    }

    $this->parameters[$param] = $value;

    return $this;
  }

  public function hasParameter ($param, $value)
  {
    return isset($this->parameters[$param]);
  }

  public function getProducts ()
  {
    return $this->getPagination()->getIterator();
  }

  protected function getSortOptions()
  {
    return [
      'column' => $this->options['sort_column'],
      'order' => $this->options['sort_order'],
      'in_customer_region_first' => $this->options['sort_in_customer_region_first']
    ];
  }
}