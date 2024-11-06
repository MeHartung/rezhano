<?php

namespace StoreBundle\Resolver\Product;

use StoreBundle\Entity\Store\Catalog\Product\Product;

class PurchasablePublicationResolver implements ProductPublicationResolverInterface
{
  public function canPublish (Product $product)
  {
    return $product->isPurchasable();
  }
}