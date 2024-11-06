<?php

namespace StoreBundle\Service\Product\ProductPrice;

use StoreBundle\Entity\Store\Catalog\Product\Product;

class ProductPrice implements ProductPriceInterface
{
  private $product;
  private $price;

  public function __construct (Product $product)
  {
    $this->product = $product;
    $this->price = $product->getPrice();
  }

  /**
   * @return float
   */
  public function getPrice ()
  {
    return $this->price;
  }

  /**
   * @param $price float
   * @return $this
   */
  public function setPrice($price)
  {
    $this->price = $price;
    return $this;
  }

  /**
   * @return Product
   */
  public function getProduct ()
  {
    return $this->product;
  }
}