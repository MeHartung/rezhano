<?php

namespace AppBundle\DataAdapter\User;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\User\User;

class UserDataAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param $subject User
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'firstname' => $subject->getFirstName(),
      'lastname' => $subject->getLastName(),
      'middlename' => $subject->getMiddleName(),
      'fullname' => $subject->getFullName(),
      'phone' => $subject->getPhone(),
      'email' => $subject->getEmail(),
      //Статус аутентификации пользователя
      'authenticated' => in_array('ROLE_USER', $subject->getRoles())
    ];
  }

  public function getModelName ()
  {
    return 'User';
  }

  public function supports ($subject)
  {
    return $subject instanceof User;
  }
}