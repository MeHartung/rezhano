<?php

namespace Tests\StoreBundle\Controller\Profile;

use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Order\OrderFixture;
use Tests\DataFixtures\PaymentMethodFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class ProfileControllerTest extends StoreWebTestCase
{

  public function testViewAction()
  {
    $this->getClient()->request('GET', '/cabinet/profile');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Неавтoризованы и попали в ЛК');

    /*
     * Сначала проверим, что не имея заказов нам не вылетит случайная 500
     */
    $this->logIn();
    $this->getClient()->request('GET', '/cabinet/profile');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
    $this->appendFixture(new PaymentMethodFixture());
    $this->appendFixture(new OrderFixture());

    /*
     * А теперь проверим и с заказами
     */
    $this->getClient()->request('GET', '/cabinet/profile');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
  }

  public function testViewActionXmlHttpRequest()
  {
    $this->getClient()->request('GET', '/cabinet/profile', [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ]);
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Неавтoризованы и попали в ЛК');

    /*
     * Сначала проверим, что не имея заказов нам не вылетит случайная 500
     */
    $this->logIn();
    $this->getClient()->request('GET', '/cabinet/profile', [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ]);
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
    $this->appendFixture(new PaymentMethodFixture());
    $this->appendFixture(new OrderFixture());

    /*
     * А теперь проверим и с заказами
     */
    $this->getClient()->request('GET', '/cabinet/profile', [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ]);
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    /*
     * Теперь проверим, что ошибки формы обрабатываются
     */
    $this->getClient()->request('GET', '/cabinet/profile', [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ], json_encode([
      'city' => 'Поселок городского типа "Дом у дороги"',
    ]));
    $this->assertSame(400, $this->getClient()->getResponse()->getStatusCode());
  }
}