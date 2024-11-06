<?php

namespace StoreBundle\Entity\Text\Dialog;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use StoreBundle\Entity\User\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="dialog_messages")
 */
class DialogMessage
{
  /**
   * @var integer
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  protected $id;

  /**
   * @var Dialog
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Text\Dialog\Dialog", cascade={"persist"})
   * @ORM\JoinColumn(name="dialog_id", onDelete="CASCADE")
   */
  protected $dialog;

  /**
   * @var string
   * @ORM\Column(type="text", nullable=false)
   */
  protected $message;

  /**
   * @var User|null
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", nullable=true, onDelete="SET NULL")
   */
  protected $user;

  /**
   * @var string
   * @ORM\Column(type="string", nullable=true)
   */
  protected $userName;

  /**
   * @var string
   * @ORM\Column(type="string", nullable=true)
   */
  protected $userEmail;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime")
   * @Gedmo\Timestampable(on="create")
   */
  protected $createdAt;

  /**
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }

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
  public function setDialog (Dialog $dialog)
  {
    $this->dialog = $dialog;
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

  /**
   * @return null|User
   */
  public function getUser ()
  {
    return $this->user;
  }

  /**
   * @param null|User $user
   * @return $this
   */
  public function setUser (User $user=null)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return string
   */
  public function getUserName ()
  {
    return $this->userName;
  }

  /**
   * @param string $userName
   * @return $this
   */
  public function setUserName ($userName)
  {
    $this->userName = $userName;
    return $this;
  }

  /**
   * @return string
   */
  public function getUserEmail ()
  {
    return $this->userEmail;
  }

  /**
   * @param string $userEmail
   * @return $this
   */
  public function setUserEmail ($userEmail)
  {
    $this->userEmail = $userEmail;
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

  public function __toString ()
  {
    return $this->getMessage();
  }
}