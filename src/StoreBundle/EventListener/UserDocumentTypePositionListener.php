<?php

namespace StoreBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use StoreBundle\Entity\Document\UserDocumentType;

class UserDocumentTypePositionListener
{
  public function prePersist(LifecycleEventArgs $event)
  {
    $subject = $event->getEntity();

    if ($subject instanceof UserDocumentType)
    {
      $this->calcPosition($subject, $event->getEntityManager());
    }
  }

  public function preUpdate(PreUpdateEventArgs $event)
  {
    $subject = $event->getEntity();

    if ($subject instanceof UserDocumentType
      && ($event->hasChangedField('positionIndividual') || $event->hasChangedField('positionJuridical') || $event->hasChangedField('positionEnterpreneur'))
    )
    {
      $this->calcPosition($subject, $event->getEntityManager());
    }
  }

  public function postRemove(LifecycleEventArgs $event)
  {
    $subject = $event->getEntity();

    if ($subject instanceof UserDocumentType)
    {
      $this->calcPosition($subject, $event->getEntityManager());
    }
  }

  protected function calcPosition(UserDocumentType $documentType, EntityManager $entityManager)
  {
    $docs = $entityManager->getRepository('StoreBundle:Document\UserDocumentType')->findAll();
    $individuals = [];
    $juridicals = [];
    $enterpreneurs = [];
    $lastPositions = [];

    /** @var UserDocumentType $doc */
    foreach ($docs as $doc)
    {
      if ($doc->getId() === $documentType->getId())
      {
        $doc = $documentType;
      }

      if ($doc->isShowEnterpreneur())
      {
        $enterpreneurs[] = $doc;
      }

      if ($doc->isShowIndividual())
      {
        $individuals[] = $doc;
      }

      if ($doc->isShowJuridical())
      {
        $juridicals[] = $doc;
      }

      $lastPositions[$doc->getId()] = [
        'enterpreneur' => $doc->getPositionEnterpreneur(),
        'individual' => $doc->getPositionIndividual(),
        'juridical' => $doc->getPositionJuridical(),
      ];
    }

    usort($enterpreneurs, [$this, 'sortEnterpreneurs']);
    usort($juridicals, [$this, 'sortJuridical']);
    usort($individuals, [$this, 'sortIndividual']);

    /** @var UserDocumentType $enterpreneur */
    foreach ($enterpreneurs as $i => $enterpreneur)
    {
      $enterpreneur->setPositionEnterpreneur($i);
    }
    /** @var UserDocumentType $juridical */
    foreach ($juridicals as $i => $juridical)
    {
      $juridical->setPositionJuridical($i);
    }
    /** @var UserDocumentType $individual */
    foreach ($individuals as $i => $individual)
    {
      $individual->setPositionIndividual($i);
    }

    $needFlush = false;

    /** @var UserDocumentType $doc */
    foreach ($docs as $doc)
    {
      if ($doc->getId() === $documentType->getId())
      {
        continue;
      }

      if ($doc->isShowEnterpreneur() || $doc->isShowEnterpreneur() || $doc->isShowJuridical())
      {
        if ($doc->getPositionJuridical() !== $lastPositions[$doc->getId()]['juridical']
          || $doc->getPositionEnterpreneur() !== $lastPositions[$doc->getId()]['enterpreneur']
          || $doc->getPositionIndividual() !== $lastPositions[$doc->getId()]['individual']
        )
        {
          $entityManager->persist($doc);
          $needFlush = true;
        }
      }
    }

    if ($needFlush)
    {
      $entityManager->flush();
    }
  }

  /**
   * @param $a UserDocumentType
   * @param $b UserDocumentType
   * @return mixed
   */
  public function sortEnterpreneurs($a, $b)
  {
    return $a->getPositionEnterpreneur() < $b->getPositionEnterpreneur() ? -1 : 1;
  }

  /**
   * @param $a UserDocumentType
   * @param $b UserDocumentType
   * @return mixed
   */
  public function sortJuridical($a, $b)
  {
    return $a->getPositionJuridical() < $b->getPositionJuridical() ? -1 : 1;
  }

  /**
   * @param $a UserDocumentType
   * @param $b UserDocumentType
   * @return mixed
   */
  public function sortIndividual($a, $b)
  {
    return $a->getPositionIndividual() < $b->getPositionIndividual() ? -1 : 1;
  }
}