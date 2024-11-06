<?php

namespace StoreBundle\Resolver\Product;

use StoreBundle\Entity\Store\Catalog\Product\Product;

class ProductPublicationManager
{
  /**
   * @var array|ProductPublicationResolverInterface[]
   */
  private $resolvers;

  public function __construct ()
  {
    $this->resolvers = [];
  }

  /**
   * @param Product $product
   * @return boolean
   */
  public function canPublish(Product $product)
  {
    foreach ($this->resolvers as $resolver)
    {
      if (!$resolver->canPublish($product))
      {
        return false;
      }
    }

    return true;
  }

  /**
   * @param ProductPublicationResolverInterface $productPublicationResolver
   * @return $this
   */
  public function addPublicationResolver(ProductPublicationResolverInterface $productPublicationResolver)
  {
    $this->resolvers[] = $productPublicationResolver;
    return $this;
  }
}