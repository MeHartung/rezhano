<?php

namespace StoreBundle\Service\Product\ProductPrice\ProductPriceModificator;


use StoreBundle\Service\Product\ProductPrice\ProductPriceInterface;

class PriceRounderModification implements ProductPriceModificatorInterface
{
  /**
   * @inheritdoc
   */
  public function getProductPrice (ProductPriceInterface $productPrice)
  {
    $productPrice->setPrice(floor($productPrice->getPrice()));
    return $productPrice;
  }

  /**
   * @inheritdoc
   */
  public function supports (ProductPriceInterface $productPrice)
  {
    return $productPrice->getPrice() > 1000;
  }
}