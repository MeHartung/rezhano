<?php

namespace Accurateweb\LocationBundle\LocationResolver;

use Accurateweb\LocationBundle\Model\UserLocation;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionLocationResolver implements LocationResolverInterface
{
  private $session;

  public function __construct (SessionInterface $session)
  {
    $this->session = $session;
  }

  public function getUserLocation()
  {
    if (!$this->session->has('aw.location'))
    {
      return null;
    }

    $locationData = $this->session->get('aw.location');
    $location = unserialize($locationData);

    if (!$location instanceof UserLocation)
    {
      return null;
    }

    return $location;
  }
}