<?php

namespace StoreBundle\Entity\Store\Catalog\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Product\ProductRankRepository")
 * @ORM\Table(name="product_rank")
 */
class ProductRank
{
  /**
   * @var Product
   * @ORM\Id()
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product", inversedBy="productRank")
   * @ORM\JoinColumn(name="product_id")
   */
  protected $product;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default"=0})
   */
  protected $nbViews=0;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default"=0})
   */
  protected $nbCart=0;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default"=0})
   */
  protected $nbFavorites=0;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default"=0})
   */
  protected $nbBuy=0;

  /**
   * @param Product $product
   * @return $this
   */
  public function setProduct (Product $product)
  {
    $this->product = $product;
    return $this;
  }

  /**
   * @return Product
   */
  public function getProduct ()
  {
    return $this->product;
  }

  /**
   * @return int
   */
  public function getNbViews ()
  {
    return $this->nbViews;
  }

  /**
   * @param int $nbViews
   * @return $this
   */
  public function setNbViews ($nbViews)
  {
    $this->nbViews = $nbViews;
    return $this;
  }

  /**
   * @return int
   */
  public function getNbCart ()
  {
    return $this->nbCart;
  }

  /**
   * @param int $nbCart
   * @return $this
   */
  public function setNbCart ($nbCart)
  {
    $this->nbCart = $nbCart;
    return $this;
  }

  /**
   * @return int
   */
  public function getNbFavorites ()
  {
    return $this->nbFavorites;
  }

  /**
   * @param int $nbFavorites
   * @return $this
   */
  public function setNbFavorites ($nbFavorites)
  {
    $this->nbFavorites = $nbFavorites;
    return $this;
  }

  /**
   * @return int
   */
  public function getNbBuy ()
  {
    return $this->nbBuy;
  }

  /**
   * @param int $nbBuy
   * @return $this
   */
  public function setNbBuy ($nbBuy)
  {
    $this->nbBuy = $nbBuy;
    return $this;
  }
}