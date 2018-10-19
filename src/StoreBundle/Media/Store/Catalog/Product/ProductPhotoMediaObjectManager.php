<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Media\Store\Catalog\Product;

use Accurateweb\MediaBundle\Model\Gallery\MediaObjectManager;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use Doctrine\ORM\EntityManager;
use Excam\Media\Gallery\ProductPhoto\ProductPhotoRepository;

class ProductPhotoMediaObjectManager implements MediaObjectManager
{
  private $em;

  private $repository;

  public function __construct(EntityManager $em, ProductPhotoRepository $repository)
  {
    $this->em = $em;
    $this->repository = $repository;
  }

  public function persist(MediaInterface $image)
  {
    $this->em->persist($image);
  }

  public function flush()
  {
    $this->em->flush();
  }

  public function remove(MediaInterface $object)
  {
    $this->em->remove($object);
  }

  public function getRepository()
  {
    return $this->repository;
  }
}