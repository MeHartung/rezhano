<?php

namespace StoreBundle\Service\Product\ProductPrice;

use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Service\Product\ProductPrice\ProductPriceModificator\ProductPriceModificatorInterface;

class ProductPriceManager
{
  /**
   * @var ProductPriceModificatorInterface[]
   */
  private $productPriceModificators;

  /**
   * @var ProductPriceInterface[]
   */
  private $productPrices = [];

  public function __construct ()
  {
    $this->productPriceModificators = [];
    $this->productPrices = [];
  }

  /**
   * @param Product $product
   * @return float
   */
  public function getProductPrice(Product $product)
  {
    if (!isset($this->productPrices[$product->getId()]))
    {
      $productPrice = new ProductPrice($product);

      foreach ($this->productPriceModificators as $productPriceModificator)
      {
        if ($productPriceModificator->supports($productPrice))
        {
          $productPrice = $productPriceModificator->getProductPrice($productPrice);
        }
      }

      $this->productPrices[$product->getId()] = $productPrice;
    }


    return $this->productPrices[$product->getId()]->getPrice();
  }

  /**
   * Возвращает разницу между оригинальной ценой и конечной
   * @param Product $product
   * @return float
   */
  public function getProductPriceDiff(Product $product)
  {
    $originalPrice = $product->getPrice();
    $customPrice = $this->getProductPrice($product);

    return $originalPrice-$customPrice;
  }

  /**
   * Возвращает разницу в процентах между оригинальной ценой и конечной
   * @param Product $product
   * @return float
   */
  public function getProductPriceDiffPercentage(Product $product)
  {
    return $this->getProductPriceDiff($product) / $product->getPrice() * 100;
  }

  /**
   * @param ProductPriceModificatorInterface $productPriceModificator
   * @return $this
   */
  public function addProductPriceModificator(ProductPriceModificatorInterface $productPriceModificator)
  {
    $this->productPriceModificators[] = $productPriceModificator;
    return $this;
  }
}