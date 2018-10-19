<?php

namespace StoreBundle\Unit\Entity;


use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderOrderStatus;
use Tests\StoreBundle\ExcamWebTestCase;

class OrderTest extends ExcamWebTestCase
{
  protected function setUp()
  {
    parent::setUp();
    $this->appendFixture(new OrderFixtures());
  }

  /**
   * @see Order::setHasProductWithFreeDelivery
   */
  public function testHasProductWithFreeDelivery()
  {
    /** @var Order $order */
    $order = $this->getByReference('order-free-delivery');
    $this->assertTrue($order->hasProductWithFreeDelivery(), 'Товар с бесплатной доставкой, а заказ так не считает');

    $order = $this->getByReference('order');
    $this->assertNotTrue($order->hasProductWithFreeDelivery(), 'В заказе не товаров с бесплатной доставкой, но заказ считает иначе');
  }

  /**
   * Проверим, что при изменении заказа пересчитывается цена
   * https://jira.accurateweb.ru/browse/EXCAM-156
   */
  public function updateSubtotalTest()
  {
    $order = $this->getByReference('order');

    $oldTotal = $order->getTotal();
    $shipCost = $order->getShippingCost();
    #Изменим цену доставки и сохраним
    $order->setShippingCost($shipCost + 5);
    $this->em->persist($order);
    $this->em->flush();
    #цена изменилась
    $totalDelta = intval($oldTotal) - intval($order->getTotal());
    $this->assertTrue($totalDelta == -5, "При обновлении цены доставки не обновилась итоговая цена");

  }
}