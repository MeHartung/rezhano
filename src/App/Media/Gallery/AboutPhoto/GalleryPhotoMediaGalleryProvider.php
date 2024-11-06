<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace App\Media\Gallery\AboutPhoto;

use Accurateweb\MediaBundle\Model\Gallery\MediaGalleryProviderInterface;
use Accurateweb\MediaBundle\Model\Gallery\MediaGallery;
use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Text\About\AboutUsImage;
use StoreBundle\Media\Store\Catalog\Product\GalleryPhotoMediaObjectManager;
use StoreBundle\Repository\Store\Catalog\Product\ProductImageRepository;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use StoreBundle\Repository\Text\About\AboutUsGalleryRepository;
use StoreBundle\Repository\Text\About\AboutUsImageRepository;

class GalleryPhotoMediaGalleryProvider implements MediaGalleryProviderInterface
{
  private $repository = null;

  private $galleryRepository = null;

  private $entityManager;

  public function __construct(EntityManager $entityManager, AboutUsImageRepository  $repository, AboutUsGalleryRepository $productRepository)
  {
    $this->entityManager = $entityManager;
    $this->repository = $repository;
    $this->galleryRepository = $productRepository;
  }

  /**
   * @param $id
   * @return MediaGallery
   */
  public function provide($id)
  {
    return new MediaGallery(
      $id,
      null,
      new \StoreBundle\Media\Text\About\GalleryPhotoMediaObjectManager($this->entityManager, new GalleryPhotoRepository($id, $this->repository)),
      new GalleryPhotoMediaFactory($this->galleryRepository)
    );
  }
}