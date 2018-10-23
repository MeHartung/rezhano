<?php

namespace Tests\StoreBundle\Service;

use AccurateCommerce\Shipping\Method\App\ShippingMethodStoreFree;
use AccurateCommerce\Shipping\Shipment\Address;
use AccurateCommerce\Shipping\Shipment\Shipment;
use AccurateCommerce\Shipping\ShippingManager;
use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\Entity\Store\Order\Order;
use Tests\StoreBundle\StoreWebTestCase;

class ShippingManagerTest extends StoreWebTestCase
{
  /** @var ShippingManager */
  private $shipping_manager;

  protected function setUp ()
  {
    parent::setUp();
    $this->shipping_manager = $this->client->getContainer()->get('accurateweb.shipping.manager');
    $this->appendFixture(new OrderFixtures());
  }


  public function testGetAvailableShippingMethodsForShipment()
  {
    /** @var Order $order */
    $order = $this->getByReference('order-free-delivery');
    $address = new Address('620000', 'Екатеринбург', '', 'ул. Ленина д. 1');
    $address_to = new Address('620000', 'Екатеринбург', '', 'ул. Ленина д. 3');
    $shipment = new Shipment($order, $order->getOrderItems(), $address, $address_to);
    $methods = $this->shipping_manager->getAvailableShippingMethodsForShipment($shipment);

    $this->assertArrayHasKey(ShippingMethodStoreFree::UID, $methods, 'Нет бесплатной доставки');

    $order = $this->getByReference('order');
    $address = new Address('620000', 'Екатеринбург', '', 'ул. Ленина д. 1');
    $address_to = new Address('620000', 'Екатеринбург', '', 'ул. Ленина д. 3');
    $shipment = new Shipment($order, $order->getOrderItems(), $address, $address_to);
    $methods = $this->shipping_manager->getAvailableShippingMethodsForShipment($shipment);

    $this->assertArrayNotHasKey(ShippingMethodStoreFree::UID, $methods, 'Есть бесплатная доставка, хотя не должно быть');
  }
}