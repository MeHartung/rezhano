<?php

namespace Accurateweb\LocationBundle\Service;

use Accurateweb\LocationBundle\LocationResolver\LocationResolverInterface;
use Accurateweb\LocationBundle\Model\UserLocation;
use Doctrine\ORM\EntityRepository;

class Location
{
  private $location;
  private $location_resolvers;
  private $lastPriority;

  public function __construct ()
  {
    $this->location_resolvers = array();
    $this->lastPriority = 0;
  }

  public function addLocationResolver(LocationResolverInterface $resolver, $priority=null)
  {
    if (!$priority)
    {
      $priority = $this->lastPriority++;
    }

    $this->location_resolvers[$priority] = $resolver;
    return $this;
  }

  /**
   * @return UserLocation
   */
  public function getLocation()
  {
    if (!$this->location)
    {
      $this->location = $this->resolveLocation();
    }

    return $this->location;
  }

  /**
   * @param UserLocation $location
   * @return $this
   */
  public function setLocation(UserLocation $location)
  {
    $this->location = $location;
    return $this;
  }

  protected function resolveLocation()
  {
    $location = null;
    ksort($this->location_resolvers);

    /** @var LocationResolverInterface $resolver */
    foreach ($this->location_resolvers as $resolver)
    {
      $location = $resolver->getUserLocation();

      if ($location)
      {
        break;
      }
    }

    return $location;
  }
}