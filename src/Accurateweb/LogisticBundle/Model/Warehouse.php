<?php

namespace Accurateweb\LogisticBundle\Model;

use Accurateweb\LogisticBundle\Model\CityInterface;
use Accurateweb\LogisticBundle\Model\WarehouseInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\MappedSuperclass()
 */
abstract class Warehouse implements WarehouseInterface
{
  /**
   * @var integer
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @var string
   * @ORM\Column()
   */
  protected $name;

  /**
   * @var string
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  protected $address;

  /**
   * @var float
   * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
   */
  protected $latitude;

  /**
   * @var float
   * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
   */
  protected $longitude;

  /**
   * @var City
   */
  protected $city;

  /**
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName ()
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setName (string $name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getAddress ()
  {
    return $this->address;
  }

  /**
   * @param string $address
   * @return $this
   */
  public function setAddress (string $address)
  {
    $this->address = $address;
    return $this;
  }

  /**
   * @return float
   */
  public function getLatitude ()
  {
    return $this->latitude;
  }

  /**
   * @param float $latitude
   * @return $this
   */
  public function setLatitude (float $latitude)
  {
    $this->latitude = $latitude;
    return $this;
  }

  /**
   * @return float
   */
  public function getLongitude ()
  {
    return $this->longitude;
  }

  /**
   * @param float $longitude
   * @return $this
   */
  public function setLongitude (float $longitude)
  {
    $this->longitude = $longitude;
    return $this;
  }

  /**
   * @return CityInterface
   */
  abstract public function getCity ();
}