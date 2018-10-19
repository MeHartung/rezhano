<?php

namespace  Tests\DataFixtures\Catalog;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Product\Product;

class ProductFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $product = new Product();
    $product->setName('Экшн-камера GoPro HERO Session')
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
      ->setIsPurchasable(true);

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
      ->setIsPurchasable(true);

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
      ->setIsPurchasable(true);

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
      ->setIsPurchasable(true);

    $manager->persist($product);
    $manager->persist($productNotInStock);
    $manager->persist($productNotPublished);
    $manager->persist($productHit);
    $manager->flush();

    $this->addReference('product', $product); //просто товар
    $this->addReference('product-notInStocked', $productNotInStock); //товар не в налчии
    $this->addReference('product-notPublished', $productNotPublished); //товар не опубликован
    $this->addReference('product-hit', $productHit); //хит
  }
}