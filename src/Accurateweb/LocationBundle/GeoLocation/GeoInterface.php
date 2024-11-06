<?php

namespace Accurateweb\LocationBundle\GeoLocation;

interface GeoInterface
{
  /**
   * GeoInterface constructor.
   * @param $ip string
   */
  public function __construct ($ip);

  public function getCountryIso();

  public function getCityName();
}