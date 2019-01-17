<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace App\Media\Gallery\AboutPhoto;

use Accurateweb\MediaBundle\Model\Media\MediaFactoryInterface;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use StoreBundle\Entity\Text\About\AboutUsImage;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use StoreBundle\Repository\Text\About\AboutUsGalleryRepository;

class GalleryPhotoMediaFactory implements MediaFactoryInterface
{
  private $entityRepository;

  public function __construct(AboutUsGalleryRepository $repository)
  {
   $this->entityRepository = $repository;
  }

  /**
   * @return MediaInterface
   */
  public function create($id)
  {
    $gallery = $this->entityRepository->find($id);

    $media =  new AboutUsImage();
    $media->setGallery($gallery);

    return $media;
  }

}