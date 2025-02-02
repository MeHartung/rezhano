<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.08.17
 * Time: 15:31
 */

namespace StoreBundle\Entity\Store\Catalog\Product\Attributes\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttribute;

/**
 * Тип товара
 *
 * @ORM\Table(name="product_types")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Product\Attributes\Type\ProductTypeRepository")
 */
class ProductType
{
  /**
   * @var integer
   *
   * @ORM\Column (name="id", type="integer")
   *
   * @ORM\Id()
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * @var string
   * @ORM\Column (name="name", length=255)
   */
  private $name;

  /**
   * @var ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product", mappedBy="productType")
   */
  private $products;

  /**
   * @var ArrayCollection|ProductAttribute[]
   *
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttribute",
   *   inversedBy="productTypes", cascade={"persist", "remove"},  orphanRemoval=true)
   * @ORM\JoinTable(name="product_types_to_product_attributes",
   *      joinColumns={@ORM\JoinColumn(name="product_type_id", referencedColumnName="id", onDelete="CASCADE")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="product_attribute_id", referencedColumnName="id", onDelete="CASCADE")})
   * @ORM\OrderBy({"position" = "DESC"})
   */
  private $productAttributes;

  /**
   * @var bool
   *
   * @ORM\Column(type="boolean")
   */
  private $measured = false;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=3, nullable=true)
   */
  private $minCount = 1;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=3, nullable=true)
   */
  private $countStep = 1;

  public function __construct()
  {
    $this->productAttributes = new ArrayCollection();
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
   * @return ProductType
   */
  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param mixed $name
   * @return ProductType
   */
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return ArrayCollection
   */
  public function getProducts()
  {
    return $this->products;
  }

  /**
   * @param ArrayCollection $products
   * @return ProductType
   */
  public function setProducts($products)
  {
    $this->products = $products;
    return $this;
  }

  /**
   * @return ArrayCollection|ProductAttribute[]
   */
  public function getProductAttributes()
  {
    return $this->productAttributes;
  }

  /**
   * @param ArrayCollection $productAttributes
   * @return ProductType
   */
  public function setProductAttributes($productAttributes)
  {
    $this->productAttributes = $productAttributes;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getMeasured()
  {
    return $this->measured;
  }

  /**
   * @param mixed $measured
   * @return ProductType
   */
  public function setMeasured($measured)
  {
    $this->measured = $measured;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getMinCount()
  {
    return $this->minCount;
  }

  /**
   * @param mixed $minCount
   * @return ProductType
   */
  public function setMinCount($minCount)
  {
    $this->minCount = $minCount;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getCountStep()
  {
    return $this->countStep;
  }

  /**
   * @param mixed $countStep
   * @return ProductType
   */
  public function setCountStep($countStep)
  {
    $this->countStep = $countStep;

    return $this;
  }

  public function __toString()
  {
    return (string)$this->name;
  }

}