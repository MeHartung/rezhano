<?php

namespace StoreBundle\Unit\Service\Order;

use AccurateCommerce\Component\Checkout\Processor\CheckoutProcessor;
use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\Entity\Store\Order\Order;
use Tests\StoreBundle\StoreWebTestCase;

/**
 * @see CheckoutProcessor
 */
class CheckoutProcessorTest extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new OrderFixtures());
  }

  public function testProcess()
  {
    $checkout_processor = $this->client->getContainer()->get('store.checkout.processor');
    /** @var Order $order */
    $order = $this->getByReference('order-preorder');
    $total = $order->getTotal();
    $order = $checkout_processor->process($order, ['preserve_calculations' => true]);
    $this->getEntityManager()->refresh($order);
    $this->assertEquals($total, $order->getTotal(), 'Стоимость заказа имзенилась после оформления');
  }
}