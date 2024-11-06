<?php

namespace StoreBundle\Entity\Store\Catalog\Taxonomy;
use Doctrine\ORM\Mapping as ORM;

/**
 * Каталог моего склада
 * @ORM\Entity()
 * @ORM\Table()
 */
class MoySkladCatalog
{
  /**
   * @var integer
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;
  
  /**
   * @var string
   * @ORM\Column(type="string", length=512)
   */
  private $name;
  
  /**
   * @var string
   * @ORM\Column(type="string", length=128)
   */
  private $guid;
  
  /**
   * @var string
   * @ORM\Column(type="string", length=128, nullable=true)
   */
  private $parentGuid;
  
  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }
  
  /**
   * @param int $id
   */
  public function setId(int $id): void
  {
    $this->id = $id;
  }
  
  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }
  
  /**
   * @param string $name
   */
  public function setName(string $name): void
  {
    $this->name = $name;
  }
  
  /**
   * @return string
   */
  public function getGuid(): string
  {
    return $this->guid;
  }
  
  /**
   * @param string $guid
   */
  public function setGuid(string $guid): void
  {
    $this->guid = $guid;
  }
  
  /**
   * @return string
   */
  public function getParentGuid(): string
  {
    return $this->parentGuid;
  }
  
  /**
   * @param string $parentGuid
   */
  public function setParentGuid(string $parentGuid): void
  {
    $this->parentGuid = $parentGuid;
  }
}