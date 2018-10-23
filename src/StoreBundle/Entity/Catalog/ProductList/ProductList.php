<?php

namespace StoreBundle\Entity\Catalog\ProductList;

use Deployer\Collection\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\User\User;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="list_type")
 * @ORM\DiscriminatorMap(value={"view" = "ViewedProductList", "favorite"="FavoriteProductList"})
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Catalog\ProductList\ProductListRepository")
 * @ORM\Table(name="product_lists")
 */
abstract class ProductList
{
  /**
   * @var integer
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @var User
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  protected $products;

  /**
   * @var ProductListProduct[]
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Catalog\ProductList\ProductListProduct", mappedBy="productList",cascade={"persist", "remove"})
   */
  protected $productListProducts;

  /**
   * @var \DateTime
   * @ORM\Column(nullable=true, type="datetime")
   * @Gedmo\Timestampable(on="create")
   */
  protected $createdAt;

  public function __construct ()
  {
    $this->productListProducts = new ArrayCollection();
  }

  /**
   * @return User
   */
  public function getUser ()
  {
    return $this->user;
  }

  /**
   * @param User $user
   * @return $this
   */
  public function setUser (User $user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return Product[]|ArrayCollection
   */
  public function getProducts ()
  {
    if (!$this->products)
    {
      $products = [];

      foreach ($this->productListProducts as $productListProduct)
      {
        $products[] = $productListProduct->getProduct();
      }

      $this->products = new ArrayCollection($products);
    }

    return $this->products;
  }

  /**
   * @param Product[] $products
   * @return $this
   */
  public function setProducts ($products)
  {
    $this->products = $products;
    $lists = [];

    foreach ($products as $product)
    {
      $listProduct = new ProductListProduct();
      $listProduct->setProduct($product);
      $listProduct->setProductList($this);
      $lists[] = $listProduct;
    }

    $this->setProductListProducts($lists);
    return $this;
  }

  public function addProduct(Product $product)
  {
    if (!$this->getProducts()->contains($product))
    {
      $products = $this->getProducts();
      $products->add($product);

      $this->setProducts($products);
    }

    return $this;
  }

  public function removeProduct(Product $product)
  {
    if ($this->getProducts()->contains($product))
    {
      $products = $this->getProducts();
      $products->removeElement($product);
      $this->setProducts($products);
    }

    return $this;
  }

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
   * @return ProductListProduct[]
   */
  public function getProductListProducts ()
  {
    return $this->productListProducts;
  }

  /**
   * @param ProductListProduct[] $productListProduct
   * @return $this
   */
  public function setProductListProducts (array $productListProduct)
  {
    $this->productListProducts = $productListProduct;
    return $this;
  }
}