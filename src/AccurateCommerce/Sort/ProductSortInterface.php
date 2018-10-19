<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.09.2018
 * Time: 23:02
 */

namespace AccurateCommerce\Sort;


use Doctrine\ORM\QueryBuilder;

interface ProductSortInterface
{
  public function apply(QueryBuilder $queryBuilder);
}