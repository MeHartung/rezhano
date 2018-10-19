<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 05.02.18
 * Time: 16:52
 */

namespace StoreBundle\Entity\Store\Order\Status;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="order_status_types")
 *
 */
class OrderStatusType
{
  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $name;

  /**
   * @var boolean
   * @ORM\Column(name="is_order_active", type="boolean")
   */
  private $isOrderActive;

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
  public function setId(int $id)
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
  public function setName(string $name)
  {
    $this->name = $name;
  }

  /**
   * @return bool
   */
  public function isOrderActive(): bool
  {
    return $this->isOrderActive;
  }

  /**
   * @param bool $isOrderActive
   */
  public function setIsOrderActive(bool $isOrderActive)
  {
    $this->isOrderActive = $isOrderActive;
  }

  public function __toString()
  {
    return (string) $this->getName();
  }

}