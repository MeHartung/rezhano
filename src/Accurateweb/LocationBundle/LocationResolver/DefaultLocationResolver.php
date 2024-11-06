<?php

namespace Accurateweb\LocationBundle\LocationResolver;

use Accurateweb\LocationBundle\Model\UserLocation;

class DefaultLocationResolver implements LocationResolverInterface
{
  private $location;

  public function __construct ($cityName, $cityCode, $countryCode)
  {
    $this->location = new UserLocation();
    $this->location
      ->setCityName($cityName)
      ->setCityCode($cityCode)
      ->setCountryCode($countryCode);
  }

  public function getUserLocation()
  {
    return $this->location;
  }
}