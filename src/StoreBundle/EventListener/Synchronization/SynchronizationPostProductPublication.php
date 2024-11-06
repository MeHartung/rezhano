<?php

namespace StoreBundle\EventListener\Synchronization;

use Accurateweb\SynchronizationBundle\Event\SynchronizationScenarioEvent;
use Accurateweb\SynchronizationBundle\Model\Handler\TransferHandler;
use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Resolver\Product\ProductPublicationManager;

class SynchronizationPostProductPublication
{
  private $entityManager;
  private $productPublication;

  public function __construct (EntityManager $entityManager, ProductPublicationManager $productPublication)
  {
    $this->entityManager = $entityManager;
    $this->productPublication = $productPublication;
  }

  public function postExecute(SynchronizationScenarioEvent $event)
  {
    $scenario = $event->getScenario();

    if ($scenario->getName() == 'moy_sklad')
    {
      /** @var Product[] $products */
      $products = $this->entityManager->getRepository('StoreBundle:Store\Catalog\Product\Product')
        ->createQueryBuilder('p')
//        ->where('p.published = FALSE')
//        ->leftJoin('p.images', 'i')
//        ->addSelect('i.id as HIDDEN image')
//        ->having('image IS NULL')
        ->getQuery()->getResult();

      foreach ($products as $product)
      {
        $canPublish = $this->productPublication->canPublish($product);

        if ($canPublish != $product->isPublished())
        {
          $product->setPublished($canPublish);
          $this->entityManager->persist($product);
        }
      }

      $this->entityManager->flush();
    }
  }
}