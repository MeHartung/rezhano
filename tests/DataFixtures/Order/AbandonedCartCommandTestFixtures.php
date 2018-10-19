<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 30.03.18
 * Time: 13:57
 */

namespace Tests\DataFixtures\Order;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;

class AbandonedCartCommandTestFixtures extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $shouldNotRemovedOrder = new Order();
    $order_item = new OrderItem();
    $order_item
      ->setProduct($this->getReference('product-go-pro'))
      ->setQuantity(3);
    $shouldNotRemovedOrder
      ->setDocumentNumber('documentNumber')
      ->addOrderItem($order_item)
      ->setPaymentMethod($this->getReference('payment-cash'))
      ->setUser($this->getReference('user-admin'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_CART);

    $shouldRemovedOrder = new Order();
    $shouldRemovedOrder->setUpdatedAt(new \DateTime('1999-12-12'));
    $order_item = new OrderItem();
    $order_item
      ->setProduct($this->getReference('product-go-pro'))
      ->setQuantity(3);
    $shouldRemovedOrder
      ->setDocumentNumber('documentNumber')
      ->addOrderItem($order_item)
      ->setPaymentMethod($this->getReference('payment-cash'))
      ->setUser($this->getReference('user-admin'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_CART);



    $manager->persist($shouldNotRemovedOrder);
    $manager->persist($shouldRemovedOrder);

    $manager->flush();

    $this->setReference('should-removed-order', $shouldRemovedOrder);
    $this->setReference('should-not-removed-order', $shouldNotRemovedOrder);
  }

}