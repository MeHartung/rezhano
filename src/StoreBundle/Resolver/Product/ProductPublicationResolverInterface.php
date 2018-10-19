<?php

namespace StoreBundle\Resolver\Product;

use StoreBundle\Entity\Store\Catalog\Product\Product;

interface ProductPublicationResolverInterface
{
  /**
   * @param Product $product
   * @return boolean
   */
  public function canPublish(Product $product);
}