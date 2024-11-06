<?php

namespace StoreBundle\Entity\Store\Logistics\Delivery\Cdek;

use Doctrine\ORM\Mapping as ORM;

/**
 * CdekPickupPoint
 *
 * @ORM\Table(name="cdek_pickup_points")
 * @ORM\Entity
 */
class CdekPickupPoint
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
   * @ORM\Column(length=255, nullable=true)
   */
  private $name;

  /**
   * @var integer
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $code;

  /**
   * Город, в котором находится ПВЗ
   *
   * @var CdekCity
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity", inversedBy="pickupPoint")
   * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
   */
  private $city;

  /**
   * @var string|null
   *
   * @ORM\Column(length=6, nullable=true)
   */
  private $postcode;

  /**
   * @var string|null
   *
   * @ORM\Column(length=512)
   */
  private $address;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return CdekPickupPoint
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get code
   *
   * @return integer
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set code
   *
   * @param integer $code
   *
   * @return CdekPickupPoint
   */
  public function setCode($code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * @return CdekCity
   */
  public function getCity(): ?CdekCity
  {
    return $this->city;
  }

  /**
   * @param CdekCity $city
   */
  public function setCity(CdekCity $city): void
  {
    $this->city = $city;
  }

  /**
   * @return null|string
   */
  public function getPostcode(): ?string
  {
    return $this->postcode;
  }


  /**
   * @param string $postcode
   * @return CdekPickupPoint
   */
  public function setPostcode(string $postcode): CdekPickupPoint
  {
    $this->postcode = $postcode;
    return $this;
  }

  /**
   * @return null|string
   */
  public function getAddress(): ?string
  {
    return $this->address;
  }

  /**
   * @param null|string $address
   * @return CdekPickupPoint
   */
  public function setAddress(?string $address): CdekPickupPoint
  {
    $this->address = $address;
    return $this;
  }




}
