<?php


namespace Tests\DataFixtures\Catalog\Product\Attribute;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType;

class ProductTypeFixture extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $measuredType = new ProductType();
    $measuredType->setName('Весовой');
    $measuredType->setCountStep(0.15);
    $measuredType->setMeasured(true);
    $measuredType->setMinCount(0.15);
    
    $notMeasuredType = new ProductType();
    $notMeasuredType->setName('Не весовой');
    $notMeasuredType->setCountStep(1);
    $notMeasuredType->setMeasured(false);
    $notMeasuredType->setMinCount(1);
    
    $manager->persist($measuredType);
    $manager->persist($notMeasuredType);
    $manager->flush();
    
    $this->addReference('measured-type', $measuredType);
    $this->addReference('not-measured-type', $notMeasuredType);
    
  }
}