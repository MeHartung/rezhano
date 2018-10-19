<?php

namespace Tests\StoreBundle\Controller\Catalog;

use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class ProductControllerTest extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
  }

  public function testShowAction()
  {
    $product = $this->getReference('product');
    $this->getClient()->request('GET', sprintf('/products/%s', $product->getSlug()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $productNotInStock = $this->getReference('product-notInStocked');
    $this->getClient()->request('GET', sprintf('/products/%s', $productNotInStock->getSlug()));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode(), 'Товар не в наличии, мы его не должны видеть');

    $productNotPublished = $this->getReference('product-notPublished');
    $this->getClient()->request('GET', sprintf('/products/%s', $productNotPublished->getSlug()));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode(), 'Товар не опубликован, мы его не должны видеть');
  }
}