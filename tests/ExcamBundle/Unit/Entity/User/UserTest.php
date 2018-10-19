<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 04.04.2018
 * Time: 18:54
 */

namespace Tests\StoreBundle\Unit\Entity\User;


use StoreBundle\Entity\User\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
  public function testGetFullName()
  {
    $user = new User();

    $user->setFirstName('TestFirstName');
    $user->setLastName('TestLastName');
    $user->setMiddleName('TestMiddleName');

    $this->assertEquals('TestFirstName TestLastName TestMiddleName', $user->getFullName());

    $user->setLastName('');
    $user->setMiddleName('');

    $this->assertEquals('TestFirstName', $user->getFullName());

    $user->setMiddleName('TestMiddleName');

    $this->assertEquals('TestFirstName TestMiddleName', $user->getFullName());
  }
}