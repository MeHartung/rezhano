<?php

namespace Tests\DataFixtures\Order;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;

class OrderFixture extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $order = new Order();
    $order_item = new OrderItem();
    $order_item
      ->setProduct($this->getReference('product'))
      ->setQuantity(3);
    $order
      ->setDocumentNumber('documentNumber')
      ->addOrderItem($order_item)
     # ->setPaymentMethod($this->getReference('payment-cash'))
      ->setUser($this->getReference('user-admin'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_COMPLETE);
    
    $orderInCart = new Order();
    $order_item = new OrderItem();
    $order_item
      ->setProduct($this->getReference('product'))
      ->setQuantity(3);
    $orderInCart
      ->setDocumentNumber('order-in-cart')
      ->addOrderItem($order_item)
      ->setUser($this->getReference('user-admin'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_CART)
      ->setUid('order-in-cart');
    
    $orderOnDeliveryStep = new Order();
    $order_item = new OrderItem();
    $order_item
      ->setProduct($this->getReference('product'))
      ->setQuantity(3);
    $orderOnDeliveryStep
      ->setDocumentNumber('order-in-delivery')
      ->addOrderItem($order_item)
      ->setUser($this->getReference('user-admin'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_DELIVERY)
   #   ->setShippingMethodId('eac20e0f-056a-4c10-9f43-7bee5c47167a')
      ->setShippingDate(new \DateTime('tomorrow'))
      ->setShippingAddress('Lenina')
      ->setUid('order-in-cart');
    
    $orderWithAllWeightTypesProducts = new Order();
    $orderItemMeasured = new OrderItem();
    $orderItemMeasured->setProduct($this->getReference('product-half-kg'));
    $orderItemMeasured->setQuantity(0.450);
    $orderItemMeasured->setPrice(3650);
    
    $orderItemMeasuredTwo = new OrderItem();
    $orderItemMeasuredTwo->setProduct($this->getReference('product-half-kg'));
    $orderItemMeasuredTwo->setQuantity(1.2);
    $orderItemMeasuredTwo->setPrice(36500);
    
    $orderItemNotMeasured = new OrderItem();
    $orderItemNotMeasured->setProduct($this->getReference('product'));
    $orderItemNotMeasured->setQuantity(1);
    $orderItemNotMeasured->setPrice(500);
    
    $orderWithAllWeightTypesProducts
      ->setDocumentNumber('order-order-order-order-order-order')
      ->addOrderItem($orderItemMeasured)
      ->addOrderItem($orderItemMeasuredTwo)
      ->addOrderItem($orderItemNotMeasured)
      ->setUser($this->getReference('user-admin'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_DELIVERY)
      ->setShippingMethod($this->getReference('shipping-method-pickup'));
    $orderWithAllWeightTypesProducts->setPaymentMethod($this->getReference('payment-cash'));
    
    $manager->persist($order);
    $manager->persist($orderInCart);
    $manager->persist($orderOnDeliveryStep);
    $manager->flush();
    
    $this->setReference('order', $order); //оформленный заказ
    $this->setReference('order-in-cart', $orderInCart); // не оформленный заказ
    $this->setReference('order-all-types-products', $orderWithAllWeightTypesProducts); // не оформленный заказ
    $this->setReference('order-in-delivery', $orderOnDeliveryStep); // корзина, прошедшая шаг доставки
  }
}