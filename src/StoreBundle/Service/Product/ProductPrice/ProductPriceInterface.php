<?php

namespace StoreBundle\Service\Product\ProductPrice;


interface ProductPriceInterface
{
  /**
   * @return float
   */
  public function getPrice();

  /**
   * @return object Purchasable
   */
  public function getProduct();

  /**
   * @param $price float
   * @return mixed
   */
  public function setPrice($price);
}