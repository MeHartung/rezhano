<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 14.09.2017
 * Time: 17:02
 */

namespace AccurateCommerce\Sort;


use Accurateweb\LocationBundle\Service\Location;
use Doctrine\ORM\QueryBuilder;

class ProductSort implements ProductSortInterface
{
  private $column;

  private $order;

  public function __construct($column, $order)
  {
    $this->column = $column;
    $this->order = $order;
  }

  public function apply(QueryBuilder $queryBuilder)
  {
    switch ($this->column)
    {
//      case 'price':
//      {
//        $queryBuilder->addOrderBy('p.price', $this->order == 'desc' ? 'desc' : 'asc');
//        break;
//      }
      case 'rank':
      {
        $queryBuilder->addOrderBy('p.rank', $this->order == 'desc' ? 'desc' : 'asc');
        break;
      }
      case 'sale':
      {
        $queryBuilder->addOrderBy('p.sale', $this->order == 'desc' ? 'desc' : 'asc');
        break;
      }
      case 'novice':
      {
        $queryBuilder->addOrderBy('p.novice', $this->order == 'desc' ? 'desc' : 'asc');
        break;
      }
    }
  }

  /**
   * @return mixed
   */
  public function getColumn()
  {
    return $this->column;
  }

  /**
   * @return mixed
   */
  public function getOrder()
  {
    return $this->order;
  }


}