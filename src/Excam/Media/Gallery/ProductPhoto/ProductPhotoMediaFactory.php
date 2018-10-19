<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Excam\Media\Gallery\ProductPhoto;

use Accurateweb\MediaBundle\Model\Media\MediaFactoryInterface;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

class ProductPhotoMediaFactory implements MediaFactoryInterface
{
  private $entityRepository;

  public function __construct(ProductRepository $repository)
  {
   $this->entityRepository = $repository;
  }

  /**
   * @return MediaInterface
   */
  public function create($id)
  {
    $product = $this->entityRepository->find($id);

    $media =  new ProductImage();
    $media->setProduct($product);

    return $media;
  }

}