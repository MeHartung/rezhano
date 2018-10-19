<?php

namespace StoreBundle\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\User\User;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Notification\NotificationRepository")
 * @ORM\MappedSuperclass()
 * @ORM\DiscriminatorMap(value={"text"="TextNotification", "dialog"="DialogNotification", "order"="OrderNotification"})
 * @ORM\DiscriminatorColumn(name="type")
 * @ORM\InheritanceType(value="JOINED")
 */
abstract class Notification
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User")
   * @ORM\JoinColumn(name="user_id")
   */
  protected $user;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   * @Gedmo\Timestampable(on="create")
   */
  protected $createdAt;

  /**
   * @var \DateTime|null
   *
   * @ORM\Column(name="readAt", type="datetime", nullable=true)
   */
  protected $readAt;

  /**
   * @var boolean
   * @ORM\Column(name="is_read", type="boolean", nullable=false, options={"default"=false})
   */
  protected $read=false;

  /**
   * Get id.
   *
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }

  /**
   * @return User
   */
  public function getUser ()
  {
    return $this->user;
  }

  /**
   * @param User $user
   * @return $this
   */
  public function setUser ($user)
  {
    $this->user = $user;
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
  public function getReadAt ()
  {
    return $this->readAt;
  }

  /**
   * @param \DateTime|null $readAt
   * @return $this
   */
  public function setReadAt ($readAt)
  {
    $this->readAt = $readAt;
    return $this;
  }

  /**
   * @return bool
   */
  public function isRead ()
  {
    return $this->read;
  }

  /**
   * @param bool $read
   * @return $this
   */
  public function setRead ($read)
  {
    $this->read = $read;
    return $this;
  }

  /**
   * @return string
   */
  abstract public function getType();

  /**
   * @return string
   */
  abstract public function getAuthor();

  /**
   * @return string
   */
  abstract public function getMessage();

  /**
   * @return string
   */
  abstract public function getTitle();
}
