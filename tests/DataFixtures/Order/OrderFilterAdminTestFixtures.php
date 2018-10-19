<?php

namespace Tests\DataFixtures\Order;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;

class OrderFilterAdminTestFixtures extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $orderOne = new Order();
    $order_item = new OrderItem();
    $order_item
      ->setProduct($this->getReference('product-go-pro'))
      ->setQuantity(3);
    $orderOne
      ->setDocumentNumber('documentNumber')
      ->addOrderItem($order_item)
      ->setPaymentMethod($this->getReference('payment-cash'))
      ->setCheckoutStateId(Order::CHECKOUT_STATE_COMPLETE);

    $orderTwo = clone $orderOne;
    $orderThree = clone $orderOne;

    $manager->persist($orderOne);
    $manager->persist($orderTwo);
    $manager->persist($orderThree);
    $manager->flush();

    $this->setReference('order-for-sort-1', $orderOne);
    $this->setReference('order-for-sort-2', $orderTwo);
    $this->setReference('order-for-sort-3', $orderThree);
 }

}