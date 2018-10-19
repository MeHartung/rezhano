<?php

namespace StoreBundle\Repository\Store\Logistics\Delivery\Warehouse;

use Accurateweb\LogisticBundle\Model\WarehouseRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class WarehouseRepository extends EntityRepository implements WarehouseRepositoryInterface
{
  public function findActiveWarehouses ()
  {
    return $this->findAll();
  }
}