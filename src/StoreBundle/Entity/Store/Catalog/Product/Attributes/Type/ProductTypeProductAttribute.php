<?php
///**
// * Created by PhpStorm.
// * User: evgeny
// * Date: 08.08.17
// * Time: 15:45
// */
//
//namespace StoreBundle\Entity\Store\Catalog\Product\Attributes\Type;
//
//use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * Таблица для реализации связи многие ко многим таблиц product_types и product_attributes
// *
// * @ORM\Table(name="product_type_product_attributes")
// * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Product\Attributes\Type\ProductTypeProductAttributeRepository")
// */
//class ProductTypeProductAttribute
//{
//
//  /**
//   * @var int
//   *
//   * @ORM\Column(name="id", type="integer")
//   * @ORM\Id()
//   * @ORM\GeneratedValue()
//   */
//  private $id;
//
//  /**
//   * @var int
//   *
//   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType", inversedBy="productTypeProductAttributes")
//   */
//  private $productType;
//
//  /**
//   * @var int
//   *
//   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttribute", inversedBy="productTypeProductAttributes")
//   */
//  private $productAttribute;
//
//
//  /**
//   * @return int
//   */
//  public function getId()
//  {
//    return $this->id;
//  }
//
//  /**
//   * @param int $id
//   */
//  public function setId($id)
//  {
//    $this->id = $id;
//  }
//
//  /**
//   * @return int
//   */
//  public function getProductType()
//  {
//    return $this->productType;
//  }
//
//  /**
//   * @param int $productType
//   */
//  public function setProductType($productType)
//  {
//    $this->productType = $productType;
//  }
//
//  /**
//   * @return int
//   */
//  public function getProductAttribute()
//  {
//    return $this->productAttribute;
//  }
//
//  /**
//   * @param int $productAttribute
//   */
//  public function setProductAttribute($productAttribute)
//  {
//    $this->productAttribute = $productAttribute;
//  }
//
//  public function __toString()
//  {
//    return (string)$this->productAttribute;
//  }
//
//
//}