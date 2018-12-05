<?php

namespace Accurateweb\MoyskladIntegrationBundle\Model\Logistic;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MoySkladWarehouse
 * Описывает склад моего склада
 * @ORM\MappedSuperclass()
 */
class MoySkladWarehouse
{
  /**
   * @var int
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  protected $id;
  
  /**
   * id склада на стороне МС
   * @var string|null
   * @ORM\Column(type="string")
   */
  protected $externalId;
  
  /**
   * Имя склада
   * @var string|null
   * @ORM\Column(type="string")
   */
  protected $name;
  
  /**
   * Описание склада
   * @var string|null
   * @ORM\Column(type="text", nullable=true)
   */
  protected $description;
  
  /**
   * Код склада на стороне МС
   * Это НЕ число
   * @var string|null
   * @ORM\Column(type="string", nullable=true)
   */
  protected $code;
  
  /**
   * Добавлен ли Склад в архив
   * @var boolean|null
   * @ORM\Column(type="boolean")
   */
  protected $archived = false;
  
  /**
   * Родительский склад (Группа)
   * МС не даёт нормального дерева, потому пока юзается имя родителя
   * @var string|null
   * @ORM\Column(type="string", nullable=true)
   */
  protected $parent;
  
  /**
   * Группа Склада
   * @var string|null
   * @ORM\Column(type="string", nullable=true)
   */
  protected $pathName;
  
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
   * @return null|string
   */
  public function getExternalId(): ?string
  {
    return $this->externalId;
  }
  
  /**
   * @param null|string $externalId
   */
  public function setExternalId(?string $externalId): void
  {
    $this->externalId = $externalId;
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
  
  /**
   * @return null|string
   */
  public function getCode(): ?string
  {
    return $this->code;
  }
  
  /**
   * @param null|string $code
   */
  public function setCode(?string $code): void
  {
    $this->code = $code;
  }
  
  /**
   * @return bool|null
   */
  public function getArchived(): ?bool
  {
    return $this->archived;
  }
  
  /**
   * @param bool|null $archived
   */
  public function setArchived(?bool $archived): void
  {
    $this->archived = $archived;
  }
  
  /**
   * @return null|string
   */
  public function getParent(): ?string
  {
    return $this->parent;
  }
  
  /**
   * @param null|string $parent
   */
  public function setParent(?string $parent): void
  {
    $this->parent = $parent;
  }
  
  /**
   * @return null|string
   */
  public function getPathName(): ?string
  {
    return $this->pathName;
  }
  
  /**
   * @param null|string $pathName
   */
  public function setPathName(?string $pathName): void
  {
    $this->pathName = $pathName;
  }
  
  public function __toString()
  {
    return $this->getId() ? $this->getName() : '';
  }
}