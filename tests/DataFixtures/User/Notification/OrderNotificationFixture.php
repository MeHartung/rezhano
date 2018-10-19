<?php

namespace Tests\DataFixtures\User\Notification;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Notification\OrderNotification;

class OrderNotificationFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $order = $this->getReference('order');
    $notification = new OrderNotification();
    $notification->setOrder($order);
    $notification->setUser($this->getReference('user-admin'));
    $notification->setMessage('Заказ создан');
    $notification->setTitle('Заказ создан');
    $this->setReference('notification-order');
    $manager->persist($notification);
    $manager->flush();
  }

}