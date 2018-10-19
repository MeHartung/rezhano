<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.09.2018
 * Time: 23:00
 */

namespace AccurateCommerce\Sort;


interface ProductSortFactoryInterface
{
  public function create(array $options = []);
}