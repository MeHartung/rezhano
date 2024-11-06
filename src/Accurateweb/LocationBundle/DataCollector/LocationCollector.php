<?php

namespace Accurateweb\LocationBundle\DataCollector;

use Accurateweb\LocationBundle\Service\Location;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class LocationCollector implements DataCollectorInterface
{
  /**
   * @var \Accurateweb\LocationBundle\Model\UserLocation
   */
  private $location;

  public function __construct (Location $location)
  {
    $this->location = $location->getLocation();
  }

  public function collect (Request $request, Response $response, \Exception $exception = null)
  {
  }

  public function getName ()
  {
    return 'aw.location.collector';
  }

  public function getLocation()
  {
    return $this->location;
  }
}