<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.08.17
 * Time: 15:29
 */


namespace StoreBundle\Entity\Store\Catalog\Product\Attributes;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Таблица атрибутов товара
 *
 * @ORM\Table(name="product_attributes")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Product\Attributes\ProductAttributeRepository")
 *
 */
class ProductAttribute
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * @var string
   * @ORM\Column(name="name", length=255)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="units", length=1020, nullable=true)
   */
  private $units;

  /**
   * @var int
   * @ORM\Column(name="type", type="integer")
   */
  private $type;

  /**
   * @var int
   *
   * @ORM\Column(name="value_type", type="integer")
   */
  private $valueType;
  
  /**
   * @var integer
   * @ORM\Column(type="integer")
   * @Gedmo\SortablePosition()
   */
  private $position;

//  /**
//   * @var ArrayCollection
//   *
//   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductTypeProductAttribute",
//   *   mappedBy="productAttribute",  cascade={"persist", "remove"}, orphanRemoval=true)
//   */
//  private $productTypeProductAttributes;

  /**
   * @var ArrayCollection
   *
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType", mappedBy="productAttributes")
   */
  private $productTypes;

  /**
   * @var ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttributeValue",
   *   mappedBy="productAttribute", cascade={"persist", "remove"}, orphanRemoval=true)
   *
   */
  private $productAttributeValues;
  
  /**
   * Значение флагак определяет, должен ли аттрибут выводиться в фильтре
   * @var bool|null
   * @ORM\Column(type="boolean", options={"default": "1"})
   */
  private $showInFilter;
  
  /**
   * Значение флагак определяет, должен ли аттрибут выводиться в товаре
   * @var bool|null
   * @ORM\Column(type="boolean", options={"default": "1"})
   */
  private $showInProduct;
  
  
  public function __construct()
  {
    $this->productAttributeValues = new ArrayCollection();
    $this->productTypes = new ArrayCollection();
  }

  /**
   * @return ArrayCollection
   */
  public function getProductTypes()
  {
    return $this->productTypes;
  }

  /**
   * @param ArrayCollection $productTypes
   * @return ProductAttribute
   */
  public function setProductTypes($productTypes)
  {
    $this->productTypes = $productTypes;

    return $this;
  }



  /**
   * @return ArrayCollection
   */
  public function getProductAttributeValues()
  {
    return $this->productAttributeValues;
  }

  public function setProductAttributeValues($values)
  {
    $this->productAttributeValues = $values;

  }
  /**
   * @param ProductAttributeValue $value
   */
  public function addProductAttributeValue(ProductAttributeValue $value)
  {
    //echo 1;die;
    $this->productAttributeValues->add($value);

    $value->setProductAttribute($this);

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
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  public function __toString()
  {
    return (string)$this->name;
  }

  /**
   * @return string
   */
  public function getUnits()
  {
    return $this->units;
  }

  /**
   * @param string $units
   */
  public function setUnits($units)
  {
    $this->units = $units;
  }

  /**
   * @return int
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param int $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * @return int
   */
  public function getValueType()
  {
    return $this->valueType;
  }

  /**
   * @param int $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  
  /**
   * @return bool|null
   */
  public function getShowInFilter(): ?bool
  {
    return $this->showInFilter;
  }
  
  /**
   * @param bool|null $showInFilter
   */
  public function setShowInFilter(?bool $showInFilter): void
  {
    $this->showInFilter = $showInFilter;
  }
  
  /**
   * @return bool|null
   */
  public function getShowInProduct(): ?bool
  {
    return $this->showInProduct;
  }
  
  /**
   * @param bool|null $showInProduct
   */
  public function setShowInProduct(?bool $showInProduct): void
  {
    $this->showInProduct = $showInProduct;
  }
  
  /**
   * @return int
   */
  public function getPosition(): ?int
  {
    return $this->position;
  }
  
  /**
   * @param int $position
   */
  public function setPosition(?int $position): void
  {
    $this->position = $position;
  }
}