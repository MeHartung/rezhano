<?php

namespace Tests\StoreBundle\Controller\User;

use Tests\StoreBundle\ExcamWebTestCase;

/**
 * @see \FOS\UserBundle\Controller\SecurityController
 */
class SecurityControllerTest extends ExcamWebTestCase
{
  public function testAdminSection()
  {
    $this->client->request('GET', '/admin/dashboard');
    $this->assertSame(302, $this->client->getResponse()->getStatusCode());

    $this->logIn($this->getByReference('user-customer'), ['ROLE_USER']);
    $this->client->request('GET', '/admin/dashboard');
    $this->assertSame(403, $this->client->getResponse()->getStatusCode());

    $this->logIn($this->getByReference('user-admin'), ['ROLE_ADMIN']);
    $this->client->request('GET', '/admin/dashboard');
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $this->logIn('user-customer', ["ROLE_USER"]);
    $this->client->request('GET',  '/admin/api/image_move', ['position'=> 4, 'image_id' => 8]);
    $this->assertSame(403, $this->client->getResponse()->getStatusCode(),
                      "Юзер может перемещать изображения в товаре.");
  }
}