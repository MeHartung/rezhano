<?php

namespace StoreBundle\EventListener\Notification;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use StoreBundle\Entity\Notification\DialogNotification;
use StoreBundle\Entity\Text\Dialog\Dialog;

/*
 * Добавляет уведомление для диалога
 *   (Добавляет диалог на страницу уведомлений)
 */
class DialogNotificationListener
{
  public function postUpdate(LifecycleEventArgs $event)
  {
    $dialog = $event->getEntity();

    if ($dialog instanceof Dialog)
    {
      $this->updateNotification($dialog, $event->getEntityManager());
    }
  }

  public function postPersist(LifecycleEventArgs $event)
  {
    $dialog = $event->getEntity();

    if ($dialog instanceof Dialog)
    {
      $this->updateNotification($dialog, $event->getEntityManager());
    }
  }

  public function updateNotification(Dialog $dialog, EntityManager $entityManager)
  {
    $notification = $dialog->getNotification();

    if (!$notification)
    {
      $notification = new DialogNotification();
      $dialog->setNotification($notification);
      $notification->setUser($dialog->getCreator());
      $notification->setCreatedAt(new \DateTime());
      $notification->setRead(true); //Отметим, чтобы не отображался как не прочитанный
      $notification->setReadAt(null);

      $entityManager->persist($notification);
      $entityManager->flush($notification);
    }
  }
}