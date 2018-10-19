<?php

namespace StoreBundle\Location;

use Accurateweb\LocationBundle\LocationResolver\LocationResolverInterface;
use Accurateweb\LocationBundle\Model\UserLocation;
use StoreBundle\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StoredCityLocationResolver implements LocationResolverInterface
{
  private $tokenStorage;

  public function __construct (TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  /**
   * @inheritdoc
   */
  public function getUserLocation ()
  {
    $token = $this->tokenStorage->getToken();

    if (!$token)
    {
      return null;
    }

    $user = $token->getUser();

    if (!$user instanceof User)
    {
      return null;
    }

    $city = $user->getCity();

    if (!$city)
    {
      return null;
    }

    $location = new UserLocation();
    $location
      ->setCityName($city->getName())
      ->setCityCode($city->getCode());

    return $location;
  }
}