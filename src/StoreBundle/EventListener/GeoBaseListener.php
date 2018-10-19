<?php

namespace StoreBundle\EventListener;


use AccurateCommerce\GeoLocation\Geo;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class GeoBaseListener
{
  private $geo;

  public function __construct (Geo $geo)
  {
    $this->geo = $geo;
  }

  public function onKernelResponse (FilterResponseEvent $event)
  {
    $response = $event->getResponse();
    $data = $this->geo->get_value();
    // create cookie
    $cookie = new Cookie('geobase', serialize($data), time() + 3600 * 24 * 7, '/');
    // set cookie in response
    $response->headers->setCookie($cookie);
  }
}