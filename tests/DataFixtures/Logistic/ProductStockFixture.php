<?php

namespace Tests\DataFixtures\Logistic;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Logistics\Warehouse\ProductStock;

class ProductStockFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $stock = new ProductStock();
    $stock
      ->setProduct($this->getReference('product'))
      ->setValue(50)
      ->setReservedValue(0)
      ->setWarehouse($this->getReference('warehouse-ekb'));

    $manager->persist($stock);
    $manager->flush();
  }
}