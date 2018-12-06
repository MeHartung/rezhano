<?php

namespace StoreBundle\Resolver\Product;

use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;

/*
 * Товар доступен к публикации, только если есть изображение
 */
class ImageExistsResolver implements ProductPublicationResolverInterface
{
  private $mediaStorage;

  public function __construct (MediaStorageInterface $mediaStorage)
  {
    $this->mediaStorage = $mediaStorage;
  }

  public function canPublish (Product $product)
  {
    if ($product->getMainImage() === null)
    {
      return false;
    }

    return $this->mediaStorage->exists($product->getMainImage());
  }
}