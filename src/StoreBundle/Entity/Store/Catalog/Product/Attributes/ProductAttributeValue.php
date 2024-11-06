<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.08.17
 * Time: 16:26
 */
namespace StoreBundle\Entity\Store\Catalog\Product\Attributes;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Таблица для хранения значений атрибутов
 * @ORM\Table(name="product_attribute_value")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Product\Attributes\ProductAttributeValueRepository")
 */
class ProductAttributeValue
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   *
   * @ORM\GeneratedValue()
   * @ORM\Id()
   *
   */
  private $id;

  /**
   * @var ProductAttribute
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttribute",
   *   inversedBy="productAttributeValues")
   * @ORM\JoinColumn(name="product_attribute_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $productAttribute;

  /**
   * @var Product[]|ArrayCollection
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product", mappedBy="productAttributeValues")
   */
  private $products;

  /**
   * @var string
   *
   * @ORM\Column(name="value", type="text")
   * @Assert\NotBlank()
   */
  private $value;

  public function __construct()
  {
    $this->products = new ArrayCollection();
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return ProductAttribute
   */
  public function getProductAttribute()
  {
    return $this->productAttribute;
  }

  /**
   * @param ProductAttribute $productAttribute
   */
  public function setProductAttribute($productAttribute)
  {
    $this->productAttribute = $productAttribute;

  }


  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * @return ArrayCollection|Product[]
   */
  public function getProducts ()
  {
    return $this->products;
  }

  /**
   * @param ArrayCollection|Product[] $products
   * @return $this
   */
  public function setProducts ($products)
  {
    $this->products = $products;
    return $this;
  }

  public function __toString()
  {
    return (string)$this->value;
  }


}