<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 26.03.18
 * Time: 12:23
 */

namespace StoreBundle\Unit\View;


use StoreBundle\Entity\User\User;
use Tests\StoreBundle\ExcamWebTestCase;

/**
 * Class AdminHeaderLink
 * @package StoreBundle\Unit\View
 * https://jira.accurateweb.ru/browse/EXCAM-199
 * Логика отображения лежит в twig шаблоне (исп. app.user)
 */
class AdminHeaderLink extends ExcamWebTestCase
{

  public function setUp()
  {
    parent::setUp();
  }

  public function testUserIsAdmin()
  {
    $this->logIn();
    $crawler = $this->client->request("GET", "/");
    # .control-panel__link - класс навбара
    $this->assertSame(1, $crawler->filter(".control-panel__link")->count(),
              "Админ не видит ссылку на раздел администрирования.");
    $this->assertContains('Перейти в раздел администрирования', $this->client->getResponse()->getContent(),
              "Админ не видит ссылку на раздел администрирования.");
  }

  public function testUserIsNotAdmin()
  {
    $usrClub = $this->getByReference('user-customer');
    $usrWholesale = $this->getByReference('user-wholesale');

   // $this->client->request("GET", "/logout");
    $this->logIn($usrClub, $usrClub->getRoles());
    $crawler = $this->client->request("GET", "/");

    $this->assertSame(0, $crawler->filter(".control-panel__link")->count(),
                      "Пользователь с ролью ROLE_CLUB увидел ссылку на админку.");
    $this->assertNotContains('Перейти в раздел администрирования', $this->client->getResponse()->getContent(),
                      "Пользователь с ролью ROLE_CLUB увидел ссылку на админку.");

    $this->logIn($usrWholesale, $usrWholesale->getRoles());
    $crawler = $this->client->request("GET", "/");

    $this->assertSame(0, $crawler->filter(".control-panel__link")->count(),
                      "Пользователь с ролью ROLE_WHOLESALE увидел ссылку на админку.");
    $this->assertNotContains('Перейти в раздел администрирования', $this->client->getResponse()->getContent(),
                      "Пользователь с ролью ROLE_WHOLESALE увидел ссылку на админку.");
  }


  public function testUserIsNotAuth()
  {
    $crawler = $this->client->request("GET", "/logout");
    $this->assertSame(0, $crawler->filter(".control-panel__link")->count(),
      "Пользователь неавторизованный увидел ссылку на админку.");
    $this->assertNotContains('Перейти в раздел администрирования', $this->client->getResponse()->getContent(),
      "Пользователь неавторизованный увидел ссылку на админку.");
  }

}