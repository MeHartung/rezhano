<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 30.03.18
 * Time: 10:49
 */

namespace Tests\StoreBundle\Service;


use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use Accurateweb\SettingBundle\Service\SettingServiceInterface;
use StoreBundle\DataFixtures\Setting\SettingFixtures;
use StoreBundle\Entity\Setting;
use Tests\StoreBundle\ExcamWebTestCase;

class SettingManagerTest extends ExcamWebTestCase
{
  /** @var  SettingManagerInterface */
  protected $settingService;

  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new SettingFixtures());
    $this->settingService = $this->client->getContainer()->get('aw.settings.manager');
  }

  public function testGetSettingValue()
  {
    /**
     * Настройка есть
     */
    $setting = $this->getByReference('default-order-abandoned-cart-age');
    $value = $this->settingService->getValue($setting->getName());
    /** Метод вернул верное значение */
    $this->assertEquals(30, $value);

    /**
     * Настройки нет
     */
    $this->expectException('Accurateweb\SettingBundle\Exception\SettingNotFoundException');
    $this->settingService->getValue("уходи");
  }
}