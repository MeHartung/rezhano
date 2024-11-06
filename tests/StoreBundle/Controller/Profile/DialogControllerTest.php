<?php

namespace Tests\StoreBundle\Controller\Profile;

use StoreBundle\Entity\Text\Dialog\Dialog;
use Tests\DataFixtures\User\Dialog\DialogFixture;
use Tests\StoreBundle\StoreWebTestCase;

class DialogControllerTest extends StoreWebTestCase
{
  public function testMessageListAction ()
  {
    $this->appendFixture(new DialogFixture());
    /** @var Dialog $dialog */
    $dialog = $this->getReference('dialog');
    $this->getClient()->request('GET', sprintf('/api/dialog/message/%s', $dialog->getId()));
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());

    $this->logIn();
    $this->getClient()->request('GET', sprintf('/api/dialog/message/%s', $dialog->getId()));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $this->getClient()->request('GET', sprintf('/api/dialog/message/%s', 0));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode());
  }

  public function testAddMessageAction ()
  {
    $this->appendFixture(new DialogFixture());
    /** @var Dialog $dialog */
    $dialog = $this->getReference('dialog');

    $data = [
      'message' => 'Hi!',
    ];

    $this->getClient()->request('POST', sprintf('/api/dialog/message/%s', $dialog->getId()), [], [], [], json_encode($data));
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());

    $this->logIn();
    $this->getClient()->request('POST', sprintf('/api/dialog/message/%s', $dialog->getId()), [], [], [], json_encode($data));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $this->getClient()->request('POST', sprintf('/api/dialog/message/%s', 0), [], [], [], json_encode($data));
    $this->assertSame(404, $this->getClient()->getResponse()->getStatusCode());
  }
}