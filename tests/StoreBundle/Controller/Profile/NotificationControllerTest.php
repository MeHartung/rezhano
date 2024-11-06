<?php

namespace Tests\StoreBundle\Controller\Profile;

use Tests\DataFixtures\User\Dialog\DialogFixture;
use Tests\DataFixtures\User\Notification\DialogNotificationFixture;
use Tests\DataFixtures\User\Notification\TextNotificationFixture;
use Tests\StoreBundle\StoreWebTestCase;

class NotificationControllerTest extends StoreWebTestCase
{
  public function testListAction ()
  {
    /*
     * Неавторизованный
     */
    $this->getClient()->request('GET', '/api/notice');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());

    /*
     * Авторизованный, без уведомлений
     */
    $this->logIn();
    $this->getClient()->request('GET', '/api/notice');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
    $data = json_decode($this->getClient()->getResponse()->getContent(), true);
    $this->assertCount(0, $data);

    /*
     * Авторизованный, с уведомлениями
     */
    $this->appendFixture(new DialogFixture());
    $this->appendFixture(new DialogNotificationFixture());
    $this->appendFixture(new TextNotificationFixture());
    $this->getClient()->request('GET', '/api/notice');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
    $data = json_decode($this->getClient()->getResponse()->getContent(), true);
    $this->assertNotCount(0, $data);
  }

  public function testReadAction ()
  {
    $this->appendFixture(new DialogFixture());
    $this->appendFixture(new DialogNotificationFixture());
    $this->appendFixture(new TextNotificationFixture());

    $textNotification = $this->getReference('notification-text-info-read');
    $dialogNotification = $this->getReference('notification-dialog-read');

    $this->getClient()->request('GET', sprintf('/api/notice/%s', $textNotification->getId()));
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());
    $this->getClient()->request('GET', sprintf('/api/notice/%s', $dialogNotification->getId()));
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());

    $this->logIn();
    $this->getClient()->request('GET', sprintf('/api/notice/%s', $textNotification->getId()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
    $this->getClient()->request('GET', sprintf('/api/notice/%s', $dialogNotification->getId()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $this->getClient()->request('GET', sprintf('/api/notice/%s', 0));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode());
  }

  public function testUpdateAction()
  {
    $this->appendFixture(new DialogFixture());
    $this->appendFixture(new DialogNotificationFixture());
    $this->appendFixture(new TextNotificationFixture());

    $textNotification = $this->getReference('notification-text-info-no-read');
    $dialogNotification = $this->getReference('notification-dialog-no-read');
    $data = [
      'read' => true,
    ];

    $this->getClient()->request('POST', sprintf('/api/notice/%s', $textNotification->getId()), [], [], [], json_encode($data));
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());
    $this->getClient()->request('POST', sprintf('/api/notice/%s', $dialogNotification->getId()), [], [], [], json_encode($data));
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());

    $this->logIn();
    $this->getClient()->request('POST', sprintf('/api/notice/%s', $textNotification->getId()), [], [], [], json_encode($data));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
    $this->getClient()->request('POST', sprintf('/api/notice/%s', $dialogNotification->getId()), [], [], [], json_encode($data));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
  }
}