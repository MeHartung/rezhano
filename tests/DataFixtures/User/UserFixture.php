<?php

namespace Tests\DataFixtures\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\User\User;

class UserFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $user = new User();
    $user
      ->setEmail('admin@accurateweb.ru')
      ->setUsername('admin')
      ->setEnabled(true)
      ->setPlainPassword('123')
      ->setFirstName('Админ')
      ->setLastName('Админов')
      ->setMiddleName('Михайлович')
      ->setPhone('+7 (959) 595-99-59')
      ->addRole(User::ROLE_ADMIN);

    $manager->persist($user);
    $manager->flush();

    $this->setReference('user-admin', $user);
  }
}