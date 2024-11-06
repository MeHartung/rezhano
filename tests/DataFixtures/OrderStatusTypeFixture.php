<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 12.02.18
 * Time: 14:15
 */

namespace Tests\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Order\Status\OrderStatusType;

class OrderStatusTypeFixture extends Fixture
{

  public function load(ObjectManager $manager)
  {
    $typeActive = new OrderStatusType();
    $typeActive->setName('Активен');
    $typeActive->setIsOrderActive(true);
    $manager->persist($typeActive);

    $typeFinished = new OrderStatusType();
    $typeFinished->setName('Завершён');
    $typeFinished->setIsOrderActive(false);
    $manager->persist($typeFinished);

    $manager->flush();
  }

}