<?php

namespace StoreBundle\Entity\Text\Dialog;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use StoreBundle\Entity\Notification\DialogNotification;
use StoreBundle\Entity\User\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="dialogs")
 */
class Dialog
{
  const DIALOG_TYPE_QUESTION = 'customer_question';

  /**
   * @var integer
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @var DialogNotification
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Notification\DialogNotification", mappedBy="dialog", cascade={"persist"})
   */
  private $notification;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime")
   * @Gedmo\Timestampable(on="create")
   */
  private $createdAt;

  /**
   * @var \DateTime|null
   * @ORM\Column(type="datetime", nullable=true)
   * @Gedmo\Timestampable(on="update")
   */
  private $updatedAt;

  /**
   * @var User|null
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User")
   * @ORM\JoinColumn(name="user_id",nullable=true)
   */
  private $creator;

  /**
   * @var string
   * @ORM\Column(type="string", nullable=false)
   */
  private $dialogType;

  /**
   * @var DialogMessage[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Text\Dialog\DialogMessage", mappedBy="dialog", cascade={"persist"})
   * @ORM\OrderBy(value={"createdAt"="DESC"})
   */
  private $messages;

  public function __construct ()
  {
    $this->messages = new ArrayCollection();
  }

  /**
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }

  /**
   * @return DialogNotification
   */
  public function getNotification ()
  {
    return $this->notification;
  }

  /**
   * @param DialogNotification $notification
   * @return $this
   */
  public function setNotification ($notification)
  {
    $this->notification = $notification;

    if (!$notification->getDialog())
    {
      $notification->setDialog($this);
    }

    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt ()
  {
    return $this->createdAt;
  }

  /**
   * @param \DateTime $createdAt
   * @return $this
   */
  public function setCreatedAt ($createdAt)
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * @return \DateTime|null
   */
  public function getUpdatedAt ()
  {
    return $this->updatedAt;
  }

  /**
   * @param \DateTime|null $updatedAt
   * @return $this
   */
  public function setUpdatedAt ($updatedAt)
  {
    $this->updatedAt = $updatedAt;
    return $this;
  }

  /**
   * @return null|User
   */
  public function getCreator ()
  {
    return $this->creator;
  }

  /**
   * @param null|User $creator
   * @return $this
   */
  public function setCreator (User $creator=null)
  {
    $this->creator = $creator;
    return $this;
  }

  /**
   * @return string
   */
  public function getDialogType ()
  {
    return $this->dialogType;
  }

  /**
   * @param string $dialogType
   * @return $this
   */
  public function setDialogType ($dialogType)
  {
    $this->dialogType = $dialogType;
    return $this;
  }

  /**
   * @return ArrayCollection|DialogMessage[]
   */
  public function getMessages ()
  {
    return $this->messages;
  }

  /**
   * @return DialogMessage
   */
  public function getLastMessage ()
  {
    /*
     * Т.к. отсортированы так, что последнее сообщение идет первым
     */
    return $this->messages->first();
  }

  /**
   * @param ArrayCollection|DialogMessage[] $messages
   * @return $this
   */
  public function setMessages ($messages)
  {
    foreach ($messages as $message)
    {
      $this->addMessage($message);
    }

    return $this;
  }

  /**
   * @param DialogMessage $message
   * @return $this
   */
  public function addMessage(DialogMessage $message)
  {
    $this->messages->add($message);
    $message->setDialog($this);
    return $this;
  }

  /**
   * @param DialogMessage $message
   * @return $this
   */
  public function removeMessage(DialogMessage $message)
  {
    $this->messages->remove($message);
    return $this;
  }

  public function __toString ()
  {
    switch ($this->getDialogType())
    {
      case self::DIALOG_TYPE_QUESTION:
        return sprintf('Вопрос пользователя');
    }

    return 'Диалог';
  }
}