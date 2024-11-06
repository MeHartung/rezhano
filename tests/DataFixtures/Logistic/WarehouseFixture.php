<?php

namespace Tests\DataFixtures\Logistic;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Logistics\Warehouse\Warehouse;

class WarehouseFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $warehouseEkb = new Warehouse();
    $warehouseEkb
      ->setCity($this->getReference('city-ekb'))
      ->setName('Екатеринбургский склад')
      ->setAddress('ул. Ленина 1');

    $warehouseMsk = new Warehouse();
    $warehouseMsk
      ->setCity($this->getReference('city-msk'))
      ->setName('Московский склад')
      ->setAddress('ул. Ленина 1');

    $manager->persist($warehouseEkb);
    $manager->persist($warehouseMsk);
    $manager->flush();

    $this->setReference('warehouse-ekb', $warehouseEkb);
    $this->setReference('warehouse-msk', $warehouseMsk);
  }
}