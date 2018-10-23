<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 30.03.18
 * Time: 16:34
 */

namespace StoreBundle\Admin\Setting;


use StoreBundle\DataFixtures\Setting\SettingFixtures;
use StoreBundle\Entity\Setting;
use Tests\StoreBundle\StoreWebTestCase;

class SettingAdminTest extends StoreWebTestCase
{
  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new SettingFixtures());
  }

  /**
   * Как админ я должен увидеть текстовое представление настроек, а не их ID
   */
  public function testList()
  {
    $this->logIn();
    $settingUrl = $this->client->getContainer()->get('main.admin.settings')->generateUrl('list');
    $this->client->request("GET", $settingUrl);
    # Работает???
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Список настроек не работает");
    # Есть ли текстовые значения настроек
    $response = $this->client->getResponse()->getContent();
    $orderDefaultStatus = $this->getByReference('order-status-processing');

    $this->assertContains('30', $response);
    $this->assertContains($orderDefaultStatus->getName(), $response);
    $this->assertContains($this->getByReference('payment-status-not-paid')->getName(), $response);

    # Изменим имя статуса заказа и проверим, поменялось ли оно в админке
    $orderDefaultStatus->setName("Уходи");
    $this->em->persist($orderDefaultStatus);
    $this->em->flush();

    $this->em->clear();

    $settingUrl = $this->client->getContainer()->get('main.admin.settings')->generateUrl('list');
    $this->client->request("GET", $settingUrl);

    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Список настроек не работает");
    $this->assertContains($orderDefaultStatus->getName(), $this->client->getResponse()->getContent(),
                  'Изменено имя статуса, но у админа оно не обновилось');
  }

  /**
   * Как админ я могу изменить значение настройки
   */
  public function testForm()
  {
    $this->logIn();

    $setting =  $this->getByReference("default-order-status");

    $settingEditUrl = $this->client->getContainer()->get('main.admin.settings')
                                   ->generateUrl('edit',['id'=> $setting->getName()]);


    $crawler = $this->client->request('GET', $settingEditUrl);

    $tokenSonata = str_replace('_description', '',
      $crawler->filter('.control-label')->eq(0)->attr('for'));
    $tokenSonata = str_replace("_value", "", $tokenSonata);

    $token = $crawler->filter('#' . $tokenSonata . '__token')->attr('value');

    $formData = [
      $tokenSonata.'[value]' => '3',
      $tokenSonata.'[_token]' => $token,
    ];

    #Админ отправляет какие-то валидные значения в форме и узнает что всё хорошо
    $form = $crawler->filter('.btn.btn-success')->form($formData);
    $this->client->submit($form);
    $this->client->followRedirect();

    $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    $this->assertContains("Элемент успешно обновлен.", $this->client->getResponse()->getContent());
    # Админ смог изменить настройку и ничего не сломать
    $setting = $this->em->find(Setting::class, $setting->getName());
    $this->assertEquals(3, $setting->getValue(), "Настройка не изменилась");
  }

  /**
   * Как админ я не могу сам создать новую настройку
   */
  public function testCreate()
  {
    $this->logIn();

    $this->client->request('GET', "/admin/Store/setting/create");
    $this->assertSame(404, $this->client->getResponse()->getStatusCode(), "Админ смог создать настройку.");
  }

}