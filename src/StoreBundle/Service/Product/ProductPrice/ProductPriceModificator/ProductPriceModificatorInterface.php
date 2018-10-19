<?php

namespace StoreBundle\Service\Product\ProductPrice\ProductPriceModificator;

use StoreBundle\Service\Product\ProductPrice\ProductPriceInterface;

interface ProductPriceModificatorInterface
{
  /**
   * @param ProductPriceInterface $productPrice
   * @return ProductPriceInterface
   */
  public function getProductPrice(ProductPriceInterface $productPrice);

  /**
   * @param ProductPriceInterface $productPrice
   * @return boolean
   */
  public function supports(ProductPriceInterface $productPrice);
}