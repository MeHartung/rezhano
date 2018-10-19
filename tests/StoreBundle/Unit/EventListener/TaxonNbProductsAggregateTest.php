<?php

namespace Tests\StoreBundle\Unit\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Logistic\CdekCityFixture;
use Tests\DataFixtures\Logistic\ProductStockFixture;
use Tests\DataFixtures\Logistic\WarehouseFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class TaxonNbProductsAggregateTest extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
    $this->appendFixture(new CdekCityFixture());
    $this->appendFixture(new WarehouseFixture());
    $this->appendFixture(new ProductStockFixture());
  }

  public function testAggregate()
  {
    /** @var Product $product */
    $product = $this->getReference('product');
    /** @var Taxon $taxon */
    $taxon = $this->getReference('taxon-avto');
    $this->assertEquals(1, $taxon->getNbProducts());

    $product->setTaxons(new ArrayCollection());
    $this->getEntityManager()->persist($product);
    $this->getEntityManager()->flush();
    $this->assertEquals(0, $taxon->getNbProducts());
  }
}