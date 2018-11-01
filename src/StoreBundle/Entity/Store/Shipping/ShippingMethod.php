<?php

namespace StoreBundle\Entity\Store\Shipping;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class ShippingMethod
 *
 * @package StoreBundle\Entity\Store\Shipping
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Shipping\ShippingMethodRepository")
 */
class ShippingMethod
{
  /**
   * @var integer|null
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;
  
  /**
   * @var string|null
   * @ORM\Column(type="string", length=64)
   */
  private $uid;
  
  /**
   * @var string|null
   * @ORM\Column(type="string", length=128)
   */
  private $name;
  
  
  /**
   * @var string|null
   * @ ORM\Column(type="string", length=128)
   */
  private $embeddedCalculatorCode;
  
  /**
   * @var string|null
   * @ORM\Column(type="text", nullable=true)
   */
  private $help;
  
  /**
   * @var integer|null
   * @ORM\Column(type="integer", nullable=true)
   */
  private $cost;
  
  /**
   * @var integer|null
   * @ORM\Column(type="integer", nullable=true)
   */
  private $freeDeliveryThreshold;
  
  /**
   * @var integer|null
   * @ORM\Column(type="integer")
   * @Gedmo\SortablePosition()
   */
  private $position;
  
  /**
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }
  
  /**
   * @param int|null $id
   */
  public function setId(?int $id): void
  {
    $this->id = $id;
  }
  
  /**
   * @return null|string
   */
  public function getUid(): ?string
  {
    return $this->uid;
  }
  
  /**
   * @param null|string $uid
   */
  public function setUid(?string $uid): void
  {
    $this->uid = $uid;
  }
  
  /**
   * @return null|string
   */
  public function getName(): ?string
  {
    return $this->name;
  }
  
  /**
   * @param null|string $name
   */
  public function setName(?string $name): void
  {
    $this->name = $name;
  }
  
  /**
   * @return null|string
   */
  public function getEmbeddedCalculatorCode(): ?string
  {
    return $this->embeddedCalculatorCode;
  }
  
  /**
   * @param null|string $embeddedCalculatorCode
   */
  public function setEmbeddedCalculatorCode(?string $embeddedCalculatorCode): void
  {
    $this->embeddedCalculatorCode = $embeddedCalculatorCode;
  }
  
  /**
   * @return null|string
   */
  public function getHelp(): ?string
  {
    return $this->help;
  }
  
  /**
   * @param null|string $help
   */
  public function setHelp(?string $help): void
  {
    $this->help = $help;
  }
  
  /**
   * @return int|null
   */
  public function getPosition(): ?int
  {
    return $this->position;
  }
  
  /**
   * @param int|null $position
   */
  public function setPosition(?int $position): void
  {
    $this->position = $position;
  }
  
  /**
   * @return null|string
   */
  public function getCost(): ?string
  {
    return $this->cost;
  }
  
  /**
   * @param null|string $cost
   */
  public function setCost(?string $cost): void
  {
    $this->cost = $cost;
  }
  
  public function __toString()
  {
    return $this->getName() ? $this->getName() : '';
  }
  
  /**
   * @return int|null
   */
  public function getFreeDeliveryThreshold(): ?int
  {
    return $this->freeDeliveryThreshold;
  }
  
  /**
   * @param int|null $freeDeliveryThreshold
   */
  public function setFreeDeliveryThreshold(?int $freeDeliveryThreshold): void
  {
    $this->freeDeliveryThreshold = $freeDeliveryThreshold;
  }
  
  public function toArray()
  {
    return
      [
        'id' => $this->getId(),
        'uid' => $this->getUid(),
        'name' => $this->getName(),
        'cost' => $this->getCost(),
        'free_delivery_threshold'=> $this->getFreeDeliveryThreshold(),
        'help' => $this->getHelp()
      ];
  }
}