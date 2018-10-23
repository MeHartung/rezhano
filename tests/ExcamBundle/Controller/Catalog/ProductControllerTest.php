<?php

namespace Tests\StoreBundle\Controller\Catalog;

use StoreBundle\Entity\Store\Catalog\Product\Product;
use Tests\StoreBundle\StoreWebTestCase;

class ProductControllerTest extends StoreWebTestCase
{
  /**
   * https://jira.accurateweb.ru/browse/EXCAM-184
   */
  public function testPreorderButton()
  {
    /** @var Product $product */
    $product = $this->getByReference('product-preorder');
    $crawler = $this->getClient()->request('GET', '/products/'.$product->getSlug());
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
    $btn = $crawler->filter('.preorder-product');

    $this->assertCount(1, $btn);
  }

  /**
   * @dataProvider dataRolesPrices
   */
  public function testShowByRole($user, $price)
  {
    if ($user)
    {
      $this->logIn($this->getByReference($user));
    }
    /** @var Product $product */
    $product = $this->getByReference('product-role');
    $crawler = $this->getClient()->request('GET', sprintf('/products/%s', $product->getSlug()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $product_price = $crawler->filter('span.MemberPrice')->text();
    $product_price = preg_replace('/[^\d]/', '', $product_price);
    $this->assertEquals($price, $product_price, 'Цена у товара не соответствует его роли');

    $meta_price = $crawler->filter('meta[itemprop=price]')->attr('content');
    $this->assertEquals($price, $meta_price, 'Цена в Meta неправильная');

    $credit = $crawler->filter('.kupivkredit-price')->text();
    $this->assertEquals(round($price * 0.0586), $credit, 'Кредитная сумма рассчитана неверно'); //0.0586 - какая-то магия
  }

  /**
   * @dataProvider dataRolesPrices
   */
  public function testRestGet($user, $price)
  {
    if ($user)
    {
      $this->logIn($this->getByReference($user));
    }

    /** @var Product $product */
    $product = $this->getByReference('product-role');
    $this->getClient()->request('GET', sprintf('/api/products/%s', $product->getSlug()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $resp = json_decode($this->getClient()->getResponse()->getContent(), true);
    $this->assertEquals($price, $resp['price']);
    $this->assertEquals(5000, $resp['originalPrice']);
  }

  public function testFreeDeliveryLabel()
  {
    /** @var Product $product */
    $product = $this->getByReference('product-whistler');
    $crawler = $this->getClient()->request('GET', '/products/'.$product->getSlug());
    $this->assertTrue($this->getClient()->getResponse()->isOk());

    $this->assertSame(1, $crawler->filter('.free-shipping')->count(), 'Нет значка "Бесплатная доставка"');

    $product = $this->getByReference('product-go-pro');
    $crawler = $this->getClient()->request('GET', '/products/'.$product->getSlug());
    $this->assertTrue($this->getClient()->getResponse()->isOk());

    $this->assertSame(0, $crawler->filter('.free-shipping')->count(), 'Есть значок "Бесплатная доставка", но не должно быть');
  }

  public function testClubPriceLabel()
  {
    /** @var Product $product */
    $product = $this->getByReference('product-role');
    $crawler = $this->getClient()->request('GET', '/products/'.$product->getSlug());
    $this->assertTrue($this->getClient()->getResponse()->isOk());
    $this->assertSame(1, $crawler->filter('.member-club_product')->count(), 'Нет значка "Клубная цена"');

    /** @var Product $product */
    $product = $this->getByReference('product-go-pro');
    $crawler = $this->getClient()->request('GET', '/products/'.$product->getSlug());
    $this->assertTrue($this->getClient()->getResponse()->isOk());
    $this->assertSame(0, $crawler->filter('.member-club_product')->count(), 'Есть значок "Клубная цена", хотя не должен быть');
  }

  public function dataRolesPrices()
  {
    return [
      'anon.'     => [null, 5000],
      'club'      => ['user-customer', 4000],
      'wholesale' => ['user-wholesale', 3000]
    ];
  }

}