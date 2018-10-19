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

  private $displayOffersInCustomerRegionFirst = false;

  public function __construct(Location $location, $column, $order, bool $displayOffersInCustomerRegionFirst = false)
  {
    $this->location = $location;
    $this->displayOffersInCustomerRegionFirst = $displayOffersInCustomerRegionFirst;

    parent::__construct($column, $order);
  }

  public function apply(QueryBuilder $queryBuilder)
  {
    if ($this->displayOffersInCustomerRegionFirst)
    {
      $city = $this->location->getLocation()->getCityName();
      if ($city)
      {
        $queryBuilder->leftJoin('p.stocks', 's')
                     ->leftJoin('s.warehouse', 'w')
                     ->leftJoin('w.city', 'c')
                     ->addGroupBy('p.id')
                     ->addSelect('(CASE WHEN (c.name = :city AND s.value > 0) THEN 1 ELSE 0 END) as HIDDEN isInCustomerRegion')
                     ->addOrderBy('isInCustomerRegion', 'desc')
                     ->setParameter('city', $city) ;
      }
    }

    parent::apply($queryBuilder);
  }

  /**
   * @return bool
   */
  public function isDisplayOffersInCustomerRegionFirst(): bool
  {
    return $this->displayOffersInCustomerRegionFirst;
  }


}