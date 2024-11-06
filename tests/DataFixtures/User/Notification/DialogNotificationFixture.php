<?php

namespace Tests\DataFixtures\User\Notification;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Text\Dialog\Dialog;

class DialogNotificationFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    foreach ($this->getDialogNotifications() as $dialogNotification)
    {
      /** @var Dialog $dialog */
      $dialog = $this->getReference($dialogNotification['dialog']);
      $notification = $dialog->getNotification();
      $notification->setRead($dialogNotification['read']);

      if ($notification->isRead())
      {
        $notification->setReadAt(new \DateTime('-1 day'));
      }

      $manager->persist($notification);
      // notification-dialog-read
      $this->setReference(sprintf('notification-dialog-%s',
        $notification->isRead() ? 'read' : 'no-read'
      ), $notification);
    }

    $manager->flush();
  }

  private function getDialogNotifications()
  {
    return [
      ['read' => true, 'dialog' => 'dialog'],
      ['read' => false, 'dialog' => 'dialog-notReaded'],
    ];
  }
}