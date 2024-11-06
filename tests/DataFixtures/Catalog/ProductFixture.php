<?php

namespace Tests\DataFixtures\Catalog;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType;
use StoreBundle\Entity\Store\Catalog\Product\Product;

class ProductFixture extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $product = new Product();
    $product
      ->setName('Экшн-камера GoPro HERO Session')
      ->setSlug('gopro-hero4-session')
      ->setPublished(true)
      ->setDescription('Самая маленькая и технически удобная экшн-камера GoPro Session')
      ->setShortDescription('Самая маленькая и технически удобная экшн-камера GoPro Session')
      ->setPrice(16000)
      ->setSku('CHDHS-102')
      ->addTaxon($this->getReference('taxon-root'))
      ->addTaxon($this->getReference('taxon-avto'))
      ->setTotalStock(50)
      ->setReservedStock(0)
      ->setExternalCode(1)
      ->setPackage(1)
      ->setUnitWeight(1)
      ->setBundle(false)
      ->setMultiplier(1)
      ->setWholesalePrice(666);
    $product->setProductType($this->getReference('not-measured-type'));
    
    $productHalfKg = new Product();
    $productHalfKg
      ->setSlug('half-kg')
      ->setPublished(true)
      ->setDescription('Пол кило')
      ->setShortDescription('Пол кило')
      ->setPrice(3600)
      ->setSku('half-kg')
      ->addTaxon($this->getReference('taxon-root'))
      ->addTaxon($this->getReference('taxon-avto'))
      ->setTotalStock(500)
      ->setReservedStock(0)
      ->setWeight(500)
      ->setUnitWeight(500)
      ->setPackage(500)
      ->setProductType($this->getReference('measured-type'));
    $productHalfKg->setMultiplier(1000);
    $productHalfKg->setUnits('гр')
      ->setExternalCode(2)
      ->setWholesalePrice(666);
    
    
    $productNotInStock = new Product();
    $productNotInStock
      ->setName('Товар не в наличии')
      ->setSlug('not-stocked')
      ->setPublished(true)
      ->setDescription('Товар не в наличии')
      ->setShortDescription('Товар не в наличии')
      ->setPrice(16000)
      ->setSku('not-stocked')
      ->addTaxon($this->getReference('taxon-root'))
      ->addTaxon($this->getReference('taxon-notStocked'))
      ->setTotalStock(50)
      ->setReservedStock(50)
      ->setExternalCode(3)
      ->setPackage(1)
      ->setUnitWeight(1)
      ->setBundle(false)
      ->setMultiplier(1)
      ->setWholesalePrice(666);
    
    $productNotPublished = new Product();
    $productNotPublished
      ->setName('Товар не опубликован')
      ->setSlug('not-published')
      ->setPublished(false)
      ->setPublicationAllowed(false)
      ->setDescription('Товар не опубликован')
      ->setShortDescription('Товар не опубликован')
      ->setPrice(16000)
      ->setSku('not-published')
      ->addTaxon($this->getReference('taxon-root'))
      ->setTotalStock(50)
      ->setReservedStock(0)
      ->setExternalCode(4)
      ->setPackage(1)
      ->setUnitWeight(1)
      ->setBundle(false)
      ->setMultiplier(1)
      ->setWholesalePrice(666);
    
    $productHit = new Product();
    $productHit->setName('Хит')
      ->setSlug('hit')
      ->setPublished(false)
      ->setPublicationAllowed(false)
      ->setDescription('Хит')
      ->setHit(true)
      ->setShortDescription('Хит')
      ->setPrice(7000)
      ->setSku('hit')
      ->addTaxon($this->getReference('taxon-root'))
      ->setTotalStock(50)
      ->setReservedStock(0)
      ->setExternalCode(5)
      ->setPackage(1)
      ->setUnitWeight(1)
      ->setBundle(false)
      ->setMultiplier(1)
      ->setWholesalePrice(666);
    
    $manager->persist($product);
    $manager->persist($productNotInStock);
    $manager->persist($productNotPublished);
    $manager->persist($productHit);
    $manager->flush();
    
    $this->addReference('product', $product); //просто товар
    $this->addReference('product-half-kg', $productHalfKg); //просто товар
    $this->addReference('product-notInStocked', $productNotInStock); //товар не в налчии
    $this->addReference('product-notPublished', $productNotPublished); //товар не опубликован
    $this->addReference('product-hit', $productHit); //хит
  }
}