<?php

namespace Accurateweb\LocationBundle\GeoLocation;

use Symfony\Component\HttpFoundation\RequestStack;

class GeoLocationFactory
{
  private static $locations = [
    'ipgeo' => 'Accurateweb\\LocationBundle\\GeoLocation\\IpGeoBase',
    'sypex' => 'Accurateweb\\LocationBundle\\GeoLocation\\Sypexgeo',
  ];

  public static function getGeo(RequestStack $requestStack, $geo=null)
  {
    $request = $requestStack->getCurrentRequest();
    $ip = '127.0.0.1';

    if ($request)
    {
      $ip = $request->getClientIp();
    }

    $class = 'Accurateweb\\LocationBundle\\GeoLocation\\IpGeoBase';

    if (isset(self::$locations[$geo]))
    {
      $class = self::$locations[$geo];
    }

    return new $class($ip);
  }
}