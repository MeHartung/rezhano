<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 26.03.18
 * Time: 10:04
 */

namespace Tests\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Setting;
use StoreBundle\Service\SettingsService;

class SettingsFixtures extends Fixture
{
  public function load (ObjectManager $manager)
  {

    $abadonedCartAge = new Setting();

    $abadonedCartAge->setName(SettingsService::SETTING_ABANDONED_CART_AGE);
    $abadonedCartAge->setAdminListViewValue(30);
    $abadonedCartAge->setValue(30);
    $abadonedCartAge->setType(SettingsService::SETTING_TYPE_STRING);
    $abadonedCartAge->setDescription('...');

    $manager->persist($abadonedCartAge);
    $manager->flush();
  }


}