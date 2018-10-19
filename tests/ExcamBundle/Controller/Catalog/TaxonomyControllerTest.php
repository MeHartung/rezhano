<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 16.01.18
 * Time: 12:42
 */

namespace Tests\StoreBundle\Controller\Catalog;

use AccurateCommerce\GeoLocation\Geo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Symfony\Bundle\FrameworkBundle\Client;
use Tests\StoreBundle\Controller\preparationForTest;
use Tests\StoreBundle\ExcamWebTestCase;

class TaxonomyControllerTest extends ExcamWebTestCase
{
  public function setUp()
  {
    parent::setUp();
  }

  public function testTaxonAction()
  {
    /** @var Taxon $taxon */
    $taxon = $this->getByReference('taxon-avto');
    $this->getClient()->request('GET', sprintf('/catalog/%s', $taxon->getSlug()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $this->getClient()->request('GET', sprintf('/catalog/%s', 'not_exists'));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode());

    $this->getClient()->request('GET', sprintf('/catalog/%s', $taxon->getSlug()), [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ]);
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
  }

  /**
  * https://jira.accurateweb.ru/browse/EXCAM-211
  */
  public function testFreeDeliveryTaxon()
  {
    $this->client->request('GET', '/catalog/besplatnaya-dostavka');
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(),
                "Что-то сломалось и бесплатная доставка отдаёт 404");

    $result = $this->client->getResponse()->getContent();

    $this->assertContains("Радар-детектор Whistler WH 338ST RU", $result,
                         "Товар с беспалтной доставкой не попал в выборку");

  }

  /**
   * Проверяем, что пустые разделы каталога отдадут 404
   * https://jira.accurateweb.ru/browse/EXCAM-174
   */
  public function testEmptyTaxon()
  {
    /**
     * Not empty taxon return 200
     */
    $this->client->request('GET', '/catalog/avtomobilnye-videoregistratory');
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(),
                        "Что-то сломалось и не пустой раздел отдаёт 404");


    # Empty taxon return 404
    $this->client->request('GET', '/catalog/empty-taxon');
    $this->assertEquals(404, $this->client->getResponse()->getStatusCode(),
                        "Пустой раздел должен отдать 404");

    /**
     * If add product in empty taxon, taxon must return 200
     * @var Product $product
     */
    $product = $this->getByReference('product-karkam');
    $product->addTaxon($this->getByReference('taxon-empty'));
    $this->getEntityManager()->persist($product);
    $this->getEntityManager()->flush();

    $this->client->request('GET', '/catalog/empty-taxon');
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(),
                        "После добавления в пустой раздел товара, он отдаёт 404");
  }
  
  public function testFreeDelivery()
  {
    $crawler = $this->client->request('GET', '/catalog/besplatnaya-dostavka');
    $this->assertTrue($this->client->getResponse()->isOk());
  }

}