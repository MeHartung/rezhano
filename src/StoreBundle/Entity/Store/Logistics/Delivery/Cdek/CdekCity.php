<?php

namespace StoreBundle\Entity\Store\Logistics\Delivery\Cdek;

use Accurateweb\LogisticBundle\Model\CityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\Store\Logistics\Warehouse\Warehouse;

/**
 * CdekCity
 *
 * @ORM\Table(name="cdek_cities")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Logistics\Delivery\Cdek\CdekCityRepository")
 */
class CdekCity implements CityInterface
{
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $name;

  /**
   * @var integer
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $region;

  /**
   * @var Warehouse[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Logistics\Warehouse\Warehouse", mappedBy="city", cascade={"persist"})
   */
  private $warehouses;

  /**
   * Пункт выдачи заказов в городе
   *
   * @var CdekPickupPoint
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekPickupPoint", mappedBy="city", cascade={"persist","remove"})
   */
  private $pickupPoint;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId ()
  {
    return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return CdekCity
   */
  public function setName ($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName ()
  {
    return $this->name;
  }

  /**
   * Set code
   *
   * @param integer $code
   *
   * @return CdekCity
   */
  public function setCode ($code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return integer
   */
  public function getCode ()
  {
    return $this->code;
  }

  /**
   * Set region
   *
   * @param string $region
   *
   * @return CdekCity
   */
  public function setRegion ($region)
  {
    $this->region = $region;

    return $this;
  }

  /**
   * Get region
   *
   * @return string
   */
  public function getRegion ()
  {
    return $this->region;
  }

  /**
   * @return ArrayCollection|Warehouse[]
   */
  public function getWarehouses ()
  {
    return $this->warehouses;
  }

  /**
   * @param ArrayCollection|Warehouse[] $warehouses
   * @return $this
   */
  public function setWarehouses ($warehouses)
  {
    $this->warehouses = $warehouses;
    return $this;
  }

  /**
   * @return CdekPickupPoint
   */
  public function getPickupPoint(): ?CdekPickupPoint
  {
    return $this->pickupPoint;
  }

  /**
   * @param CdekPickupPoint $pickupPoint
   * @return CdekCity
   */
  public function setPickupPoint(CdekPickupPoint $pickupPoint): CdekCity
  {
    $this->pickupPoint = $pickupPoint;

    if ($pickupPoint->getCity() !== $this)
    {
      $pickupPoint->setCity($this);
    }

    return $this;
  }



  public function __toString ()
  {
    if (!$this->getName())
    {
      return 'Новый город';
    }

    return $this->getName();
  }
}
