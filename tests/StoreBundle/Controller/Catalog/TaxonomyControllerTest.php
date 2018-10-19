<?php

namespace Tests\StoreBundle\Controller\Catalog;

use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class TaxonomyControllerTest extends StoreWebTestCase
{
  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
  }

  public function testTaxonAction()
  {
    /** @var Taxon $taxon */
    $taxon = $this->getReference('taxon-avto');
    $this->getClient()->request('GET', sprintf('/catalog/%s', $taxon->getSlug()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $this->getClient()->request('GET', sprintf('/catalog/%s', 'not_exists'));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode());

    $this->getClient()->request('GET', sprintf('/catalog/%s', $taxon->getSlug()), [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ]);
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
  }

  public function testFreeDeliveryTaxon()
  {
    $this->getClient()->request('GET', '/catalog/besplatnaya-dostavka');
    $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(),
                "Что-то сломалось и бесплатная доставка отдаёт 404");
  }

  /**
   * Проверяем, что пустые разделы каталога отдадут 404
   */
  public function testEmptyTaxon()
  {
    /**
     * Not empty taxon return 200
     */
    $this->getClient()->request('GET', '/catalog/avtomobilnye-videoregistratory');
    $this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode(),
                        "Что-то сломалось и не пустой раздел отдаёт 404");


    # Empty taxon return 404
    $this->getClient()->request('GET', '/catalog/empty-taxon');
    $this->assertEquals(404, $this->getClient()->getResponse()->getStatusCode(),
                        "Пустой раздел должен отдать 404");
  }
  
  public function testFreeDelivery()
  {
    $this->getClient()->request('GET', '/catalog/besplatnaya-dostavka');
    $this->assertTrue($this->getClient()->getResponse()->isOk());
  }

  public function testShowTaxonWithNotAvailableProducts()
  {
    $taxon = $this->getReference('taxon-notStocked');
    $this->getClient()->request('GET', sprintf('/catalog/%s', $taxon->getSlug()));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode(), 'Каталог с товарами, недоступными для покупки не должен отображаться');
  }

  public function testBestOffers()
  {
    $this->getClient()->request('GET', '/catalog/best-offers');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
  }

}