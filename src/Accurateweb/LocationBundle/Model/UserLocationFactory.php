<?php

namespace Accurateweb\LocationBundle\Model;

use AppBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;
use AppBundle\Repository\Store\Logistics\Delivery\Cdek\CdekCityRepository;

class UserLocationFactory
{
  private $cdekCityRepository;

  protected static $map = [
    1 => [
      'code' => 1,
      'default_city_code' => 250
    ],
    42 => [
      'code' => 42,
      'default_city_code' => 9220
    ],
    48 => [
      'code' => 48,
      'default_city_code' => 4961
    ]
  ];

  public function __construct (CdekCityRepository $cdekCityRepository)
  {
    $this->cdekCityRepository = $cdekCityRepository;
  }

  public function getUserLocationByCityCode($cityCode)
  {
    $city = $this->cdekCityRepository->findOneBy(['code' => $cityCode]);
    $userLocation = new UserLocation();
    $userLocation
      ->setCityCode($cityCode);

    /** @var $city CdekCity */
    if ($city)
    {
      $userLocation
        ->setCountryCode($city->getCountryCode())
        ->setCityName($city->getName());
    }

    return $userLocation;
  }

  public function getUserLocationByCountryCode($countryCode)
  {
    if (isset(self::$map[$countryCode]))
    {
      return $this->getUserLocationByCityCode(self::$map[$countryCode]['default_city_code']);
    }

    $location = new UserLocation();
    $location->setCountryCode($countryCode);

    return $location;
  }
}