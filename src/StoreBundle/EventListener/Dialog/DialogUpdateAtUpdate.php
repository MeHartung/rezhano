<?php

namespace StoreBundle\EventListener\Dialog;

use Doctrine\ORM\Event\LifecycleEventArgs;
use StoreBundle\Entity\Text\Dialog\DialogMessage;

class DialogUpdateAtUpdate
{
  public function postPersist(LifecycleEventArgs $event)
  {
    $message = $event->getEntity();

    if ($message instanceof DialogMessage)
    {
      $dialog = $message->getDialog();

      if ($dialog && $dialog->getUpdatedAt() !== $message->getCreatedAt())
      {
        $dialog->setUpdatedAt($message->getCreatedAt());
        $event->getEntityManager()->persist($dialog);
        $event->getEntityManager()->flush();
      }
    }
  }
}