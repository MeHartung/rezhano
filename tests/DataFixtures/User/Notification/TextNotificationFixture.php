<?php

namespace Tests\DataFixtures\User\Notification;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Notification\TextNotification;

class TextNotificationFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    foreach ($this->getTextNotifications() as $textNotification)
    {
      $notification = new TextNotification();
      $notification->setUser($this->getReference('user-admin'));
      $notification->setRead($textNotification['read']);

      if ($notification->isRead())
      {
        $notification->setReadAt(new \DateTime('-1 day'));
      }

      $notification->setNotificationType($textNotification['type']);
      $notification->setMessage($textNotification['message']);

      $manager->persist($notification);
      // notification-text-info-read
      $this->setReference(sprintf('notification-text-%s-%s',
        $notification->getNotificationType(),
        $notification->isRead() ? 'read' : 'no-read'
      ), $notification);
    }

    $manager->flush();
  }

  private function getTextNotifications()
  {
    return [
      ['read' => true, 'type' => TextNotification::NOTIFICATION_TYPE_TECH, 'message' => 'Tech Message'],
      ['read' => true, 'type' => TextNotification::NOTIFICATION_TYPE_AUCTION, 'message' => 'Auction Message'],
      ['read' => true, 'type' => TextNotification::NOTIFICATION_TYPE_INFO, 'message' => 'Info Message'],
      ['read' => false, 'type' => TextNotification::NOTIFICATION_TYPE_TECH, 'message' => 'Tech Message'],
      ['read' => false, 'type' => TextNotification::NOTIFICATION_TYPE_AUCTION, 'message' => 'Auction Message'],
      ['read' => false, 'type' => TextNotification::NOTIFICATION_TYPE_INFO, 'message' => 'Info Message'],
    ];
  }
}