<?php

namespace Accurateweb\LocationBundle\LocationResolver;

use Accurateweb\LocationBundle\GeoLocation\GeoInterface;
use Accurateweb\LocationBundle\Model\UserLocation;
use Doctrine\ORM\EntityRepository;

class GeoLocationResolver implements LocationResolverInterface
{
  private $geo;
  private $cityRepository;

  public function __construct (GeoInterface $geo, EntityRepository $cityRepository)
  {
    $this->geo = $geo;
    $this->cityRepository = $cityRepository;
  }

  public function getUserLocation()
  {
    $cityName = $this->geo->getCityName();

    if (!$cityName)
    {
      return null;
    }

    /** @var CdekCity $city */
    $city = $this->cityRepository->findOneBy(['name' => $cityName]);
    $location = new UserLocation();
    $location->setCityName($cityName);

    if ($city)
    {
      $location
        ->setCityCode($city->getId())
        ->setCountryCode($city->getCountryCode());
      return $location;
    }

    return null;
//    return $location;
  }
}