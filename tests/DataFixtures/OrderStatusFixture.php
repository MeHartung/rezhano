<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 12.02.18
 * Time: 14:01
 */

namespace Tests\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Setting;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use StoreBundle\Entity\Store\Order\Status\OrderStatusType;
use StoreBundle\Service\SettingsService;

class OrderStatusFixture extends Fixture
{

  public function load(ObjectManager $manager)
  {
    $activeStatus = $manager->getRepository(OrderStatusType::class)->findOneBy(['isOrderActive'=>true]);
    $notActiveStatus = $manager->getRepository(OrderStatusType::class)->findOneBy(['isOrderActive'=>false]);

    $firstStatus = new OrderStatus();
    $firstStatus->setName('Заказ в обработке');
    $firstStatus->setType($activeStatus);
    $manager->persist($firstStatus);

    $secondStatus = new OrderStatus();
    $secondStatus->setName('Заказ доставляется');
    $secondStatus->setType($activeStatus);
    $manager->persist($secondStatus);

    $thirdStatus = new OrderStatus();
    $thirdStatus->setName('Заказ доставлен');
    $thirdStatus->setType($activeStatus);
    $manager->persist($thirdStatus);

    $fourthStatus = new OrderStatus();
    $fourthStatus->setName('Заказ отменён');
    $fourthStatus->setType($notActiveStatus);
    $manager->persist($fourthStatus);

    $manager->flush();

    $this->addReference('order-status-processing', $firstStatus);
    $this->addReference('order-status-will-be-delivered', $secondStatus);
    $this->addReference('order-status-delivered', $thirdStatus);
    $this->addReference('order-status-cancel', $fourthStatus);
  }

}