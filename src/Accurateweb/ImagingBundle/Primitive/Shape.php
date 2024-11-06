<?php

namespace Accurateweb\ImagingBundle\Primitive;

class Shape
{
  private $location;

  public function __construct(Point $location)
  {
    $this->setLocation($location);
  }

  public function getX()
  {
    return $this->location->getX();
  }

  public function getY()
  {
    return $this->location->getY();
  }

  public function getLocation()
  {
    return clone $this->location;
  }

  public function setLocation(Point $location)
  {
    $this->location = clone $location;
  }
}