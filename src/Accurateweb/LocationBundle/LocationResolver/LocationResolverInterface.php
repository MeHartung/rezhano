<?php

namespace Accurateweb\LocationBundle\LocationResolver;


use Accurateweb\LocationBundle\Model\UserLocation;

interface LocationResolverInterface
{
  /**
   * @return UserLocation|null
   */
  public function getUserLocation();
}