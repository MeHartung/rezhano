<?php

namespace StoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class TaxonNbProductsAggregate implements EventSubscriber
{
  public function getSubscribedEvents ()
  {
    return [
      'prePersist',
      'postFlush'
    ];
  }

  public function prePersist(LifecycleEventArgs $event)
  {
    $entity = $event->getEntity();

    if ($entity instanceof Taxon)
    {
      $service = new \StoreBundle\Service\Taxon\TaxonNbProductsAggregate($event->getEntityManager());
      $service->recalculate($entity, false);
    }
  }

  /*
   * Пересчитывает количество товаров у всех категорий, которые учавствуют изменении
   *  Не сохраняет их, т.к. все ломается при этом
   */
  public function postFlush(PostFlushEventArgs $event)
  {
    $em = $event->getEntityManager();
    $uow = $em->getUnitOfWork();
    $taxons = new ArrayCollection();

    /** @var PersistentCollection $scheduledCollectionDeletion */
    foreach ($uow->getScheduledCollectionDeletions() as $scheduledCollectionDeletion)
    {
      if ($scheduledCollectionDeletion->getTypeClass()->getName() === 'StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon')
      {
        foreach ($scheduledCollectionDeletion as $taxon)
        {
          $taxons->add($taxon);
        }
      }
    }

    /** @var PersistentCollection $scheduledCollectionUpdate */
    foreach ($uow->getScheduledCollectionUpdates() as $scheduledCollectionUpdate)
    {
      if ($scheduledCollectionUpdate->getTypeClass()->getName() === 'StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon')
      {
        foreach ($scheduledCollectionUpdate as $taxon)
        {
          $taxons->add($taxon);
        }
      }
    }

    if (count($taxons))
    {
      $em = $event->getEntityManager();
      $service = new \StoreBundle\Service\Taxon\TaxonNbProductsAggregate($em);

      foreach ($taxons as $taxon)
      {
        $service->recalculate($taxon, false);
      }
    }
  }
}