<?php

namespace Accurateweb\LocationBundle\EventListener;

use Accurateweb\LocationBundle\Service\Location;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class LocationSerializer
{
  private $location;

  public function __construct (Location $location)
  {
    $this->location = $location;
  }

  public function onKernelResponse(FilterResponseEvent $event)
  {
    $user_location = $this->location->getLocation();
    $session = $event->getRequest()->getSession();

    if ($session)
    {
      $session->set('aw.location', serialize($user_location));
    }
  }
}