<?php

namespace StoreBundle\Unit\Service\Order;

use StoreBundle\Entity\Store\Order\Order;
use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Logistic\CdekCityFixture;
use Tests\DataFixtures\Logistic\ProductStockFixture;
use Tests\DataFixtures\Logistic\WarehouseFixture;
use Tests\DataFixtures\Order\OrderFixture;
use Tests\DataFixtures\PaymentMethodFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class CartToOrderConverterTest extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new CdekCityFixture());
    $this->appendFixture(new WarehouseFixture());
    $this->appendFixture(new ProductFixture());
    $this->appendFixture(new ProductStockFixture());
    $this->appendFixture(new PaymentMethodFixture());
    $this->appendFixture(new OrderFixture());
  }

  /**
   * TODO Дописать тест, проверяющий что он реально делит по городам
   */
  public function testConvert()
  {
    /** @var Order $cart */
    $cart = $this->getReference('order-in-cart');

    $converter = $this->getClient()->getContainer()->get('store.cart_to_orders.converter');
    $orders = $converter->convertToOrders($cart);

    $this->assertCount(1, $orders);
  }
}