<?php

namespace StoreBundle\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="notification_text")
 */
class TextNotification extends Notification
{
  const NOTIFICATION_TYPE_AUCTION = 'auction';
  const NOTIFICATION_TYPE_INFO = 'info';
  const NOTIFICATION_TYPE_TECH = 'tech';

  /**
   * @var string
   * @ORM\Column(name="notification_type", type="string", nullable=false)
   */
  private $notification_type;

  /**
   * @var string
   * @ORM\Column(type="text", nullable=false)
   */
  private $message;

  /**
   * @return string
   */
  public function getNotificationType ()
  {
    return $this->notification_type;
  }

  /**
   * @param string $notification_type
   * @return $this
   */
  public function setNotificationType ($notification_type)
  {
    if (!in_array($notification_type, self::getAvailableNotificationTypes()))
    {
      throw new \InvalidArgumentException(sprintf('Available notification types: [%s]', implode(', ', self::getAvailableNotificationTypes())));
    }

    $this->notification_type = $notification_type;
    return $this;
  }

  /**
   * @return string
   */
  public function getMessage ()
  {
    return $this->message;
  }

  /**
   * @param string $message
   * @return $this
   */
  public function setMessage ($message)
  {
    $this->message = $message;
    return $this;
  }

  public static function getAvailableNotificationTypes()
  {
    return [
      self::NOTIFICATION_TYPE_AUCTION => self::NOTIFICATION_TYPE_AUCTION,
      self::NOTIFICATION_TYPE_INFO => self::NOTIFICATION_TYPE_INFO,
      self::NOTIFICATION_TYPE_TECH => self::NOTIFICATION_TYPE_TECH,
    ];
  }

  public function getType ()
  {
    return $this->getNotificationType();
  }

  public function getAuthor ()
  {
    return 'Поддержка';
  }

  public function getTitle ()
  {
    return mb_strimwidth($this->getMessage(), 0, 30, '...');
  }

}