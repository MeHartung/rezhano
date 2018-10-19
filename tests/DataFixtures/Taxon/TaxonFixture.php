<?php

namespace Tests\DataFixtures\Taxon;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Controller\ProductControllerTest;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class TaxonFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $taxon = new Taxon();
    $taxon->setName('Каталог')
      ->setSlug('katalogh')
      ->setParent(null)
      ->setDescription(null)
      ->setShortName('Каталог');

    $subtaxon = new Taxon();
    $subtaxon->setName('Автомобильные видеорегистраторы')
      ->setSlug('avtomobilnye-videoregistratory')
      ->setDescription('<h2>Видеорегистраторы</h2>')
      ->setShortName('Автомобильные видеорегистраторы')
      ->setParent($taxon);

    $subtaxonEmpty = new Taxon();
    $subtaxonEmpty->setName('Empty Taxon')
      ->setSlug('empty-taxon')
      ->setDescription('<h2>Empty Taxon</h2>')
      ->setShortName('Empty')
      ->setParent($taxon);

    $orderTestTaxon = new Taxon();
    $orderTestTaxon->setName('Order Test Taxon')
      ->setSlug('order-test-taxon')
      ->setDescription('<h2>Order Test Taxon</h2>')
      ->setShortName('Sort Taxon')
      ->setParent($taxon);

    $subtaxonGps = new Taxon();
    $subtaxonGps->setName('GPS Навигаторы')
      ->setSlug('gpsnavigation')
      ->setDescription('<h2>Навигаторы</h2>')
      ->setShortName('GPS')
      ->setParent($taxon);

    $taxonWithNotStockedProducts = new Taxon();
    $taxonWithNotStockedProducts
      ->setName('Каталог с товарами, которых нет в наличии')
      ->setSlug('taxon-with-not-stocked-products')
      ->setDescription('Каталог с товарами, которых нет в наличии')
      ->setShortName('notstocked')
      ->setParent($taxon);

    $manager->persist($taxon);
    $manager->persist($subtaxon);
    $manager->persist($subtaxonEmpty);
    $manager->persist($orderTestTaxon);
    $manager->persist($subtaxonGps);
    $manager->persist($taxonWithNotStockedProducts);
    $manager->flush();

    $this->setReference('taxon-root', $taxon);
    $this->setReference('taxon-avto', $subtaxon); //Здесь есть товары
    $this->setReference('taxon-empty', $subtaxonEmpty);
    $this->setReference('taxon-sort', $orderTestTaxon);
    $this->setReference('taxon-gps', $subtaxonGps);
    $this->setReference('taxon-notStocked', $taxonWithNotStockedProducts);
  }
}