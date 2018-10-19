<?php

namespace Tests\StoreBundle\Controller\User;


use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\Entity\Store\Order\Order;
use Tests\StoreBundle\ExcamWebTestCase;

class UserOrderHistoryControllerTest extends ExcamWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new OrderFixtures());
  }

  public function testIndex()
  {
    $this->client->request('GET', '/cabinet/orders');
    $this->assertSame(302, $this->client->getResponse()->getStatusCode());

    $this->logIn();

    $this->client->request('GET', '/cabinet/orders');
    $this->assertSame(200, $this->  client->getResponse()->getStatusCode());
  }

  public function testView()
  {
    /** @var Order $order */
    $order = $this->getByReference('order');
    $user = $order->getUser();

    $this->logIn($user);

    $this->client->request('GET', '/api/cabinet/orders/'.$order->getId());
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $order = $this->getByReference('order-wholesale');

    $this->client->request('GET', '/api/cabinet/orders/'.$order->getId());
    $this->assertSame(404, $this->client->getResponse()->getStatusCode(), 'Покупатель может получить данные о чужом заказе');
  }
}