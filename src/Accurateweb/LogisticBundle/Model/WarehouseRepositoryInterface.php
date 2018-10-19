<?php

namespace Accurateweb\LogisticBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;

interface WarehouseRepositoryInterface extends ObjectRepository
{
  /**
   * @return WarehouseInterface[]|null
   */
  public function findActiveWarehouses();
}