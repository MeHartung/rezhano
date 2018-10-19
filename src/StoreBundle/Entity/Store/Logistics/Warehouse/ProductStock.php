<?php

namespace StoreBundle\Entity\Store\Logistics\Warehouse;

use Accurateweb\LogisticBundle\Model\StockableInterface;
use Accurateweb\LogisticBundle\Model\WarehouseInterface;
use Doctrine\ORM\Mapping as ORM;
use Accurateweb\LogisticBundle\Model\ProductStock as BaseProductStock;
use StoreBundle\Entity\Store\Catalog\Product\Product;

/**
 * @ORM\Entity()
 * @ORM\Table(name="product_stock", uniqueConstraints={
 *        @ORM\UniqueConstraint(name="stock_unique",
 *            columns={"product_id", "warehouse_id"})
 *    })
 */
class ProductStock extends BaseProductStock
{
  /**
   * @var integer
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  protected $id;
  /**
   * @var Warehouse
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Logistics\Warehouse\Warehouse", cascade={"persist"})
   * @ORM\JoinColumn(name="warehouse_id",onDelete="CASCADE", nullable=false)
   */
  protected $warehouse;

  /**
   * @var Product
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product", inversedBy="stocks", cascade={"persist"})
   * @ORM\JoinColumn(name="product_id",onDelete="CASCADE", nullable=false)
   */
  protected $product;

  /**
   * @return WarehouseInterface|Warehouse
   */
  public function getWarehouse ()
  {
    return $this->warehouse;
  }

  /**
   * @param Warehouse $warehouse
   * @return $this
   */
  public function setWarehouse (WarehouseInterface $warehouse)
  {
    if (!$warehouse instanceof Warehouse)
    {
      throw new \InvalidArgumentException();
    }

    $this->warehouse = $warehouse;
    return $this;
  }

  /**
   * @return StockableInterface|Product
   */
  public function getProduct ()
  {
    return $this->product;
  }

  /**
   * @param Product $product
   * @return $this
   */
  public function setProduct (StockableInterface $product)
  {
    if (!$product instanceof Product)
    {
      throw new \InvalidArgumentException();
    }

    $this->product = $product;
    return $this;
  }
}