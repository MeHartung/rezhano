<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Excam\Media\Gallery\ProductPhoto;

use Accurateweb\MediaBundle\Model\Gallery\MediaGalleryProviderInterface;
use Accurateweb\MediaBundle\Model\Gallery\MediaGallery;
use Doctrine\ORM\EntityManager;
use StoreBundle\Media\Store\Catalog\Product\ProductPhotoMediaObjectManager;
use StoreBundle\Repository\Store\Catalog\Product\ProductImageRepository;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

class ProductPhotoMediaGalleryProvider implements MediaGalleryProviderInterface
{
  private $repository = null;

  private $productRepository = null;

  private $entityManager;

  public function __construct(EntityManager $entityManager, ProductImageRepository $repository, ProductRepository $productRepository)
  {
    $this->entityManager = $entityManager;
    $this->repository = $repository;
    $this->productRepository = $productRepository;
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
      new ProductPhotoMediaObjectManager($this->entityManager, new ProductPhotoRepository($id, $this->repository)),
      new ProductPhotoMediaFactory($this->productRepository)
    );
  }
}