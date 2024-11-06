<?php

namespace StoreBundle\Resolver\Product;

use StoreBundle\Entity\Store\Catalog\Product\Product;

class ProductStockPublicationResolver implements ProductPublicationResolverInterface
{
  public function canPublish (Product $product)
  {
    return $product->getAvailableStock() > 0;
  }
}