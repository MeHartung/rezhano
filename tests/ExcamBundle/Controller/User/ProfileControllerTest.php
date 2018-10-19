<?php

namespace Tests\StoreBundle\Controller\User;

use Tests\StoreBundle\ExcamWebTestCase;

/**
 * @see \FOS\UserBundle\Controller\ProfileController
 */
class ProfileControllerTest extends ExcamWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->logIn($this->getByReference('user-admin'));
  }

  public function testCabinetRouteRedirect()
  {
    $this->client->request('GET', '/cabinet');
    $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    $this->assertEquals('/cabinet/orders', $this->client->getRequest()->get('path'));

    $this->client->followRedirect();
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    $this->assertEquals('/cabinet/orders', $this->client->getRequest()->getPathInfo());
  }

  public function testEdit()
  {
    $crawler = $this->client->request('GET', '/cabinet/profile');
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $form = $crawler->filter('.fos_user_profile_edit')->form();
    $csrf = $form->get('fos_user_profile_form[_token]')->getValue();
    $form_values = [
      'fos_user_profile_form' => [
        'firstname' => 'profiletest',
        'lastname' => 'profiletest',
        'middlename' => 'test',
        'email' => 'e123@mail.ru',
        'phone' => '+7 (959) 595-99-59',
        '_token' => $csrf
      ]
    ];

    $this->client->request('POST', $form->getUri(), $form_values);
    $this->assertTrue($this->client->getResponse()->isRedirect());

    $this->assertSame('profiletest', $this->getByReference('user-admin')->getLastname());
  }
}