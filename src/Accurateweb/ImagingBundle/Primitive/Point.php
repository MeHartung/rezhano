<?php
namespace Accurateweb\ImagingBundle\Primitive;

class Point
{
  private $x, $y;

  public function __construct($x = 0, $y = 0)
  {
    $this->setX($x);
    $this->setY($y);
  }

  public function getY()
  {
    return $this->y;
  }

  public function setY($y)
  {
    $this->y = $y;
  }

  public function getX()
  {
    return $this->x;
  }

  public function setX($x)
  {
    $this->x = $x;
  }

}
