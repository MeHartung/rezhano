<?php

namespace Tests\StoreBundle\Unit\Service\EventListener;

use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\User\User;
use Tests\StoreBundle\StoreWebTestCase;

/**
 * @see OrderTotalCalculateSubscriber
 */
class OrderTotalCalculateSubscriberTest extends StoreWebTestCase
{
  public function testAddItem()
  {
    $cart = new Order();
    $item = new OrderItem();
    $item->setProduct($this->getByReference('product-go-pro'));
    $item->setQuantity(1);
    $cart->addOrderItem($item);
    $this->getEntityManager()->persist($cart);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(16000, $cart->getTotal());

    $item->setQuantity(2);
    $this->getEntityManager()->persist($item);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(32000, $cart->getTotal());

    $item = new OrderItem();
    $item->setProduct($this->getByReference('product-karkam'));
    $item->setQuantity(1);
    $cart->addOrderItem($item);
    $this->getEntityManager()->persist($item);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);


    $this->assertEquals(39000, $cart->getTotal());
  }

  public function testPriceAfterLogin()
  {
    $cart = new Order();
    $cart
      ->setCheckoutStateId(Order::CHECKOUT_STATE_CART)
      ->setUser($this->getByReference('user-wholesale'));
    $item = new OrderItem();
    $item->setProduct($this->getByReference('product-role'));
    $item->setQuantity(1);
    $cart->addOrderItem($item);
    $this->getEntityManager()->persist($cart);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(3000, $cart->getTotal(), 'Корзина не обновилась при смене роли');

    $cart->setUser($this->getByReference('user-customer'));
    $this->getEntityManager()->persist($cart);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(4000, $cart->getTotal(), 'Корзина не обновилась при смене роли');
  }

  public function testRemoveCartItem()
  {
    $cart = new Order();
    $item = new OrderItem();
    $item->setProduct($this->getByReference('product-go-pro'));
    $item->setQuantity(1);
    $item2 = new OrderItem();
    $item2->setProduct($this->getByReference('product-karkam'));
    $item2->setQuantity(1);

    $cart->addOrderItem($item);
    $cart->addOrderItem($item2);
    $this->getEntityManager()->persist($cart);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(23000, $cart->getTotal());

    $this->getEntityManager()->remove($item2);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(16000, $cart->getTotal());
  }

  /**
   * @see OrderTotalCalculateSubscriber::preCalculateOrderItem
   * @see OrderTotalCalculateSubscriber::calculateSubtotal
   */
  public function testUserChangeRole()
  {
    /** @var Order $order */
    $order = $this->getOrder();
    $total = $order->getTotal();

    /** @var User $user */
    $user = $this->getByReference('user-customer');
    $user->removeRole(User::ROLE_CLUB);
    $this->getEntityManager()->persist($user);
    $this->getEntityManager()->flush();

    $order->setShippingAddress('Какое-то изменение, не влияющее на рассчет стоимости');
    $this->getEntityManager()->persist($order);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($order);

    $this->assertEquals($total, $order->getTotal(), 'Изменение роли покупателя влияет на рассчет стоимости оформленного заказа');

    $items = $order->getOrderItems();
    $item = $items[0];
    $item_cost = $item->getPrice();
    $item->setQuantity(2);
    $this->getEntityManager()->persist($item);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($order);
    $this->getEntityManager()->refresh($item);

    $this->assertEquals($item_cost, $item->getPrice(), 'Изменение количества товаров в заказе сбрасывает стоимость товара');
    $this->assertEquals($total - $item_cost, $order->getTotal(), 'Изменение количества товаров меняет стоимость заказа');
  }

  /**
   * @return Order
   */
  protected function getOrder()
  {
    $order_club = new Order();
    $order_item = new OrderItem();
    $order_item
      ->setProduct($this->getByReference('product-role'))
      ->setQuantity(3);
    $order_club
      ->addOrderItem($order_item)
      ->setUser($this->getByReference('user-customer'))
      ->setPaymentMethod($this->getByReference('payment-cash'))
      ->setUser($this->getByReference('user-customer'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_CART);

    $this->getEntityManager()->persist($order_club);
    $order_club->setCheckoutStateId(Order::CHECKOUT_STATE_COMPLETE);
    $this->getEntityManager()->persist($order_club);
    $this->getEntityManager()->flush();

    return $order_club;
  }
}