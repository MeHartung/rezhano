<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 30.03.18
 * Time: 15:56
 */

namespace Tests\DataFixtures\Order;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;

class OrderOrderStatusFixture extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $orderOneStatus = new OrderStatusHistory();
    $orderOneStatus->setOrder($this->getReference('order-for-sort-1'));
    $orderOneStatus->setStatus($this->getReference('order-status-processing'));

    $orderTwoStatus = new OrderStatusHistory();
    $orderTwoStatus->setOrder($this->getReference('order-for-sort-2'));
    $orderTwoStatus->setStatus($this->getReference('order-status-processing'));

    $orderThreeStatus = new OrderStatusHistory();
    $orderThreeStatus->setOrder($this->getReference('order-for-sort-3'));
    $orderThreeStatus->setStatus($this->getReference('order-status-processing'));

    $manager->persist($orderOneStatus);
    $manager->persist($orderTwoStatus);
    $manager->persist($orderThreeStatus);
    $manager->flush();

    $this->setReference('order-for-sort-status-1', $orderOneStatus);
    $this->setReference('order-for-sort-status-2', $orderTwoStatus);
    $this->setReference('order-for-sort-status-2', $orderThreeStatus);
  }
}