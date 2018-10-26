<?php

namespace StoreBundle\Entity\Store\Shipping;
use Doctrine\ORM\Mapping as ORM;

/**
 * Пунткы самовывоза
 * @ORM\Entity()
 * @ORM\Table()
 */
class PickupPoint
{
  /**
   * @var int|null
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;
  
  /**
   * @var string|null
   * @ORM\Column(type="string")
   */
  private $address;
  
  /**
   * @var string|null
   * @ORM\Column(type="string", length=128)
   */
  private $name;
  
  /**
   * @var string|null
   * @ORM\Column(type="string", length=512)
   */
  private $description;
  
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
  public function getAddress(): ?string
  {
    return $this->address;
  }
  
  /**
   * @param null|string $address
   */
  public function setAddress(?string $address): void
  {
    $this->address = $address;
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
  public function getDescription(): ?string
  {
    return $this->description;
  }
  
  /**
   * @param null|string $description
   */
  public function setDescription(?string $description): void
  {
    $this->description = $description;
  }
  
  public function __toString()
  {
    return $this->getName() ? $this->getName() : '';
  }
}