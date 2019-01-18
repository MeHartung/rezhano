<?php

namespace StoreBundle\Entity\Notification;

use StoreBundle\Entity\Store\Order\Order;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="notification_order")
 */
class OrderNotification extends Notification
{
  /**
   * @var Order
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\Order", inversedBy="notifications")
   * @ORM\JoinColumn(name="order_id", onDelete="CASCADE")
   */
  protected $order;

  /**
   * @var string
   * @ORM\Column(type="text", nullable=false)
   */
  private $message;

  /**
   * @var string
   * @ORM\Column(type="string", nullable=false)
   */
  private $title;

  public function getType ()
  {
    return 'order';
  }

  public function getAuthor ()
  {
    return 'Поддержка';
  }

  public function getMessage ()
  {
    return $this->message;
  }

  public function getTitle ()
  {
    return $this->title;
  }

  /**
   * @param Order $order
   * @return $this
   */
  public function setOrder ($order)
  {
    $this->order = $order;
    return $this;
  }

  /**
   * @return Order
   */
  public function getOrder ()
  {
    return $this->order;
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
   * @param string $title
   * @return $this
   */
  public function setTitle ($title)
  {
    $this->title = $title;
    return $this;
  }
}