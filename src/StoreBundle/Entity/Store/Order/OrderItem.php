<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Store\Order;

use Accurateweb\MediaBundle\Model\Image\Image;
use Doctrine\ORM\Mapping as ORM;
use AccurateCommerce\Shipping\Shippable\IPurchasable;
use AccurateCommerce\Shipping\Shippable\ShippableInterface;
use JMS\Serializer\Annotation as JMS;
use StoreBundle\Entity\Store\Catalog\Product\Product;

/**
 * Товар в заказе
 *
 * @package StoreBundle\Entity\Order
 *
 * @ORM\Table(name="order_items")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class OrderItem implements ShippableInterface
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * @var Order
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\Order", inversedBy="orderItems")
   * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $order;

  /**
   * @var Product
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product", inversedBy="orderItems",
   *                cascade={"persist"})
   * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
   */
  private $product;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=3)
   * @JMS\Expose
   */
  private $quantity;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2, nullable=true)
   * @JMS\Expose
   */
  private $price;


  /**
   * @var integer
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $virtuemartOrderItemId;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return OrderItem
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /**
   * @return Order
   */
  public function getOrder()
  {
    return $this->order;
  }

  /**
   * @param Order $order
   * @return OrderItem
   */
  public function setOrder($order)
  {
    $this->order = $order;

    if (!$this->order->getOrderItems()->contains($this))
    {
      $this->order->addOrderItem($this);
    }

    return $this;
  }

  /**
   * @return Product
   */
  public function getProduct()
  {
    return $this->product;
  }

  /**
   * @param Product $product
   * @return OrderItem
   */
  public function setProduct($product)
  {
    $this->product = $product;

    return $this;
  }

  /**
   * @return float
   */
  public function getQuantity()
  {
    return $this->quantity;
  }

  public function getFormattedQuantity()
  {
    return rtrim($this->formatFloat($this->getQuantity()), '0');
  }
  
  /**
   * @param int $quantity
   * @return OrderItem
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;

    return $this;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    if (null === $this->price)
    {
      $product = $this->getProduct();

      return $product ? $product->getUnitPrice() : null;
      #return $product ? $product->getPrice() : null;
    }

    return $this->price;
  }

  /**
   * @param float $price
   *
   * @return OrderItem
   */
  public function setPrice($price)
  {
    if (null === $price)
    {
      $this->price = null;
    }
    else
    {
      $this->price = (float)$price;
    }



    return $this;
  }

  public function getPurchasableId()
  {
    return $this->getProduct()->getId();
  }

  public function toJSON()
  {
    $product = $this->getProduct();

    $primary_taxon = $product->getPrimaryTaxon();
    $brand = $product->getBrand();

    $images = [];


    foreach ($product->getImages() as $image)
    { /** @var  $image Image */
      $images[] = $image->getResourceId();
    }

    return [
      'id' => $this->getId(),
      'quantity' => $this->getFormattedQuantity(),
      'price' => $this->getPrice(),
      'product_id' => $this->getPurchasableId(),
      'name' => $this->getProduct()->getName(),
      'cost' => $this->getCost(),
      'product' => $product ? [
        'name' => $product->getName(),
        'brand' => $brand ? $brand->getName() : null,
        'sku' => $product->getSku(),
        'slug' => $product->getSlug(),
        'images' => count($images)>0 ? $images : null,
        'preview_image' => $product->getThumbnailUrl('catalog_prev'),
        'taxon' => $primary_taxon?$primary_taxon->getName():'',
        'background' => $product->getBackground(),
        'isMeasured' => $product->getMeasured(),
        'units' => $product->getUnits()
      ] : null
    ];
  }

  public function getCost()
  {
    return $this->getQuantity() * $this->getPrice();
  }

  /**
   * Возвращает вес единицы в кг
   *
   * @return float
   */
  public function getWeight()
  {
    return null;
  }

  /**
   * Возвращает объем единицы в м<sup>3</sup>
   *
   * @return float
   */
  public function getVolume()
  {
    return null;
  }

  /**
   * @return Product
   */
  public function getPurchasable()
  {
    return $this->getProduct();
  }

  public function __toString()
  {
    $product = $this->getProduct();
    return $product ? (string)$product : '#'.(string)$this->getId();
  }

  /**
   * Убирает нули после запятой, если число целое.
   *
   * @param $number
   * @return string
   */
  private function formatFloat($number)
  {
    if ($number - floor($number) == 0)
    {
      return rtrim(rtrim($number, '0'), '.');
    }

    return $number;
  }
}