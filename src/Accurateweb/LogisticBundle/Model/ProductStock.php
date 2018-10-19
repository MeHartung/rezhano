<?php

namespace Accurateweb\LogisticBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\MappedSuperclass()
 */
abstract class ProductStock implements ProductStockInterface
{
  /**
   * @var WarehouseInterface
   * @ORM\Id()
   */
  protected $warehouse;

  /**
   * @var StockableInterface
   * @ORM\Id()
   */
  protected $product;

  /**
   * @var int
   * @ORM\Column(type="integer", nullable=false)
   */
  protected $value=0;

  /**
   * @var int
   * @ORM\Column(type="integer", nullable=false)
   */
  protected $reserved=0;

  /**
   * @return WarehouseInterface
   */
  abstract public function getWarehouse ();

  /**
   * @param WarehouseInterface $warehouse
   * @return $this
   */
  abstract public function setWarehouse (WarehouseInterface $warehouse);

  /**
   * @return StockableInterface
   */
  abstract public function getProduct ();

  /**
   * @param StockableInterface $product
   * @return $this
   */
  abstract public function setProduct (StockableInterface $product);

  /**
   * @return int
   */
  public function getValue ()
  {
    return $this->value;
  }

  /**
   * @param int $value
   * @return $this
   */
  public function setValue ($value)
  {
    $this->value = $value;
    return $this;
  }

  /**
   * @return int
   */
  public function getReservedValue ()
  {
    return $this->reserved;
  }

  /**
   * @param int $reserved
   * @return $this
   */
  public function setReservedValue ($reserved)
  {
    $this->reserved = $reserved;
    return $this;
  }

  public function getAvailableValue ()
  {
    return $this->value - $this->reserved;
  }
}