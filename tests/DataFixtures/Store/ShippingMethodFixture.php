<?php


namespace Tests\DataFixtures\Store;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Shipping\ShippingMethod;

class ShippingMethodFixture extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $methodPickup = new ShippingMethod();
    $methodPickup->setName('Самовывоз');
    $methodPickup->setAddress('г. Екб, улица Пушкина');
    $methodPickup->setCity('Екб');
    $methodPickup->setCost(0);
    $methodPickup->setHelp('Никакой помощи!');
    $methodPickup->setIsActive(true);
    $methodPickup->setShowAddress('Курлык');
    $methodPickup->setPosition(1);
    $methodPickup->setUid('8dc7ee8f-18f0-40af-964f-d10c3ab091a3');
    
    $manager->persist($methodPickup);
    $manager->flush();
    
    $this->addReference('shipping-method-pickup', $methodPickup);
  }
}