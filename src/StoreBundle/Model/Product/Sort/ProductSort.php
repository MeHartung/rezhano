<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.09.2018
 * Time: 22:54
 */

namespace StoreBundle\Model\Product\Sort;

use AccurateCommerce\Sort\ProductSort as BaseProductSort;
use Accurateweb\LocationBundle\Service\Location;
use Doctrine\ORM\QueryBuilder;

class ProductSort extends BaseProductSort
{
  private $location;

  public function __construct(Location $location, $column, $order)
  {
    $this->location = $location;

    parent::__construct($column, $order);
  }

  public function apply(QueryBuilder $queryBuilder)
  {
    parent::apply($queryBuilder);
  }

}