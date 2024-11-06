<?php

namespace StoreBundle\Resolver\Product;

use StoreBundle\Entity\Store\Catalog\Product\Product;

class PublicationAllowedPublicationResolver implements ProductPublicationResolverInterface
{
  public function canPublish (Product $product)
  {
    return $product->isPublicationAllowed();
  }
}