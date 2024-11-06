<?php

namespace Tests\DataFixtures\Setting;


use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Setting;

/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 30.03.18
 * Time: 10:53
 */
class SettingFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{
  public function load(ObjectManager $manager)
  {
    $settingAbandonedCartAge = new Setting();
    $settingAbandonedCartAge->setName(Setting::SETTING_ABANDONED_CART_AGE);
    $settingAbandonedCartAge->setValue(30);
    $manager->persist($settingAbandonedCartAge);

    $orderPaymentStatus = $this->getReference('payment-status-not-paid');
    $settingDefaultOrderPaymentStatus = new Setting();
    $settingDefaultOrderPaymentStatus ->setName(Setting::SETTING_DEFAULT_ORDER_PAYMENT_STATUS);
    $settingDefaultOrderPaymentStatus ->setValue($orderPaymentStatus->getId());
    $manager->persist($settingDefaultOrderPaymentStatus );

    $orderStatus = $this->getReference('order-status-processing');
    $settingDefaultOrderStatus = new Setting();
    $settingDefaultOrderStatus->setName(Setting::SETTING_DEFAULT_ORDER_STATUS);
    $settingDefaultOrderStatus->setValue($orderStatus->getId());
    $manager->persist($settingDefaultOrderStatus);

    $manager->flush();

    $this->addReference('default-order-status', $settingDefaultOrderStatus);
    $this->addReference('default-order-payment-status', $settingDefaultOrderPaymentStatus);
    $this->addReference('default-order-abandoned-cart-age', $settingAbandonedCartAge);

  }

}
