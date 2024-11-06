<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace App\Media\Gallery\AboutPhoto;

use Accurateweb\MediaBundle\Model\Gallery\MediaObjectManager;
use Accurateweb\MediaBundle\Model\Gallery\MediaRepositoryInterface;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use StoreBundle\Repository\Store\Catalog\Product\ProductImageRepository;
use StoreBundle\Repository\Text\About\AboutUsImageRepository;

class GalleryPhotoRepository implements MediaRepositoryInterface
{
  private $entityRepository;

  private $entityId;

  public function __construct($id, AboutUsImageRepository $repository)
  {
    $this->entityId = $id;
    $this->entityRepository = $repository;
  }

  public function getAll()
  {
    return $this->createQueryBuilder()->getQuery()->getResult();
  }

  public function add(MediaInterface $media)
  {
    if (!$media instanceof ProductImage)
    {
      throw new \InvalidArgumentException('media must be an instance of ProductImage');
    }

    $this->entityRepository->add($media);
  }

  public function find($id)
  {
    return $this->entityRepository->find($id);
  }

  /**
   * @return \Doctrine\ORM\QueryBuilder
   */
  protected function createQueryBuilder()
  {
    $qb = $this->entityRepository
                ->createQueryBuilder('i');

    return $qb->andWhere($qb->expr()->eq('IDENTITY(i.gallery)', $this->entityId))->orderBy('i.position');
  }
}