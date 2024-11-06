<?php

namespace Tests\StoreBundle;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Tests\DataFixtures\User\UserFixture;
use StoreBundle\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\FixtureAwareWebTestCase;

abstract class StoreWebTestCase extends FixtureAwareWebTestCase
{
  /** @var Client */
  protected $client = null;
  /** @var User|null */
  protected $user;

  /** @var EntityManagerInterface */
  protected $em;

  protected function setUp ()
  {
    $this->client = $this->getClient(true);
    $this->em = $this->getEntityManager();
    parent::setUp();
    $this->addFixture(new UserFixture());
    $this->executeFixtures();
  }

  protected function getClient($reload=false)
  {
    if (!$this->client || $reload)
    {
      $this->client = static::createClient();
    }

    return $this->client;
  }

  /**
   * @param User|null $user
   * @param array|null $roles
   */
  protected function logIn($user = null, $roles = null)
  {
    if (!$user)
    {
      $user = $this->getReference('user-admin');
    }

    if (!$roles)
    {
      $roles = ['ROLE_SUPER_ADMIN'];
    }

    $this->user = $user;

    $session = $this->getClient()->getContainer()->get('session');
    $firewallContext = 'main';

    $token = new UsernamePasswordToken($user, null, $firewallContext, $roles);
    $session->set('_security_' . $firewallContext, serialize($token));
    $session->save();

    $cookie = new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId());
    $this->getClient()->getCookieJar()->set($cookie);
    $this->getClient()->getContainer()->get('security.token_storage')->setToken($token);
  }

  /**
   * @param string $relativePath
   * @return File
   */
  protected function getResource($relativePath = '')
  {
    $dir = $this->getResourceDir();
    $filePath = sprintf('%s/%s', $dir, $relativePath);

    return new File($filePath);
  }

  protected function getResourceDir()
  {
    return sprintf('%s/Resources', __DIR__);
  }
}