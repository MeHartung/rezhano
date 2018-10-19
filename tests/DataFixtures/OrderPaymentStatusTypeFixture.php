<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 26.03.18
 * Time: 18:08
 */

namespace Tests\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatus;
use StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatusType;

class OrderPaymentStatusTypeFixture extends Fixture
{

  public function load(ObjectManager $manager)
  {

    $notPaidType = new OrderPaymentStatusType();
    $notPaidType->setName('Не оплачен');
    $notPaidType->setGuid('a9213afe-5fec-4a72-9c08-e5bb5e86beb9');

    $paidType  = new OrderPaymentStatusType();
    $paidType->setGuid('fdc5232b-e0eb-4914-8748-3e7de4a585e8');
    $paidType->setName('Оплачен');

    $manager->persist($notPaidType);
    $manager->persist($paidType);
    $manager->flush();

    $this->addReference('payment-status-type-paid', $paidType);
    $this->addReference('payment-status-type-not-paid', $notPaidType);
  }

}