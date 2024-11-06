<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 26.03.18
 * Time: 18:07
 */

namespace Tests\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class OrderPaymentStatusFixture extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $notPay = new \StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatus();
    $notPay->setType($this->getReference('payment-status-type-not-paid'));
    $notPay->setName('Не оплачен');

    $pay = new \StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatus();
    $pay->setType($this->getReference('payment-status-type-paid'));
    $pay->setName('Оплачен');

    $manager->persist($notPay);
    $manager->persist($pay);
    $manager->flush();

    $this->addReference('payment-status-paid', $pay);
    $this->addReference('payment-status-not-paid', $notPay);
  }

}