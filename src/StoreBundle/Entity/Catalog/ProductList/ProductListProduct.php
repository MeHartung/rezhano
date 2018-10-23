<?php

namespace StoreBundle\Entity\Catalog\ProductList;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use StoreBundle\Entity\Store\Catalog\Product\Product;

/**
 * @ORM\Entity()
 * @ORM\Table("product_list_product")
 */
class ProductListProduct
{
  /**
   * @var ProductList
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Catalog\ProductList\ProductList")ProductViewListener
   * @ORM\JoinColumn(name="product_list_id")
   * @ORM\Id()
   */
  private $productList;

  /**
   * @var Product
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product")
   * @ORM\JoinColumn(name="product_id")
   * @ORM\OrderBy(value={"createdAt"="DESC"})
   * @ORM\Id()
   */
  private $product;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime", nullable=true)
   * @Gedmo\Timestampable(on="create")
   */
  private $createdAt;

  /**
   * @return \DateTime
   */
  public function getCreatedAt ()
  {
    return $this->createdAt;
  }

  /**
   * @param \DateTime $createdAt
   * @return $this
   */
  public function setCreatedAt (\DateTime $createdAt)
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * @return ProductList
   */
  public function getProductList ()
  {
    return $this->productList;
  }

  /**
   * @return Product
   */
  public function getProduct ()
  {
    return $this->product;
  }

  /**
   * @param ProductList $productList
   * @return $this
   */
  public function setProductList (ProductList $productList)
  {
    $this->productList = $productList;
    return $this;
  }

  /**
   * @param Product $product
   * @return $this
   */
  public function setProduct (Product $product)
  {
    $this->product = $product;
    return $this;
  }
}