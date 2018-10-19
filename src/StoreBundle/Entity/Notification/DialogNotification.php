<?php

namespace StoreBundle\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\Text\Dialog\Dialog;

/**
 * @ORM\Entity()
 * @ORM\Table(name="notification_dialog")
 */
class DialogNotification extends Notification
{
  /**
   * @var Dialog
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Text\Dialog\Dialog", inversedBy="notification")
   * @ORM\JoinColumn(name="dialog_id", nullable=false, onDelete="CASCADE")
   */
  protected $dialog;

  /**
   * @return Dialog
   */
  public function getDialog ()
  {
    return $this->dialog;
  }

  /**
   * @param Dialog $dialog
   * @return $this
   */
  public function setDialog ($dialog)
  {
    $this->dialog = $dialog;

    if (!$dialog->getNotification())
    {
      $dialog->setNotification($this);
    }

    return $this;
  }

  public function getType ()
  {
    return 'dialog';
  }

  public function getAuthor ()
  {
    $dialog = $this->getDialog();

    if ($dialog)
    {
      $lastMessage = $dialog->getLastMessage();

      if ($lastMessage)
      {
        return $lastMessage->getUserName();
      }
    }

    return '';
  }

  public function getMessage ()
  {
    $dialog = $this->getDialog();

    if ($dialog)
    {
      $lastMessage = $dialog->getLastMessage();

      if ($lastMessage)
      {
        return $lastMessage->getMessage();
      }
    }

    return '';
  }

  public function getTitle ()
  {
    $message = $this->getMessage();
    $message = strip_tags($message);

    return mb_strimwidth($message, 0, 30, '...');
  }
}