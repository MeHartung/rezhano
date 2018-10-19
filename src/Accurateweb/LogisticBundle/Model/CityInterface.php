<?php

namespace Accurateweb\LogisticBundle\Model;


interface CityInterface
{
  /**
   * @return string
   */
  public function getName();

  /**
   * @return WarehouseInterface[]
   */
  public function getWarehouses();
}