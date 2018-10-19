<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.02.18
 * Time: 18:01
 */

namespace StoreBundle\Entity\Store\Order\Status;

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\User\User;


/**
 * Class OrderStatusHistory
 * @package StoreBundle\Entity\Store\Order\Status
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Order\Status\OrderOrderStatusRepository")
 * @ORM\Table(name="order_status_history")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderStatusHistory
{
 /**
 * @var int
 *
 * @ORM\Column(name="id", type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue
 */
  private $id;

  /**
   * @var $order Order
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\Order", inversedBy="orderStatus",
   *                cascade={"persist", "remove"})
   */
  private $order;

  /**
   * @var OrderStatus
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\Status\OrderStatus", inversedBy="orderOrderStatus",
   *                cascade={"persist"})
   */
  private $status;

  /**
   * @var string
   * @ORM\Column(name="reason", type="string", length=255, nullable=true)
   */
  private $reason;

  /**
   * @var User
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User", inversedBy="id")
   */
  private $user;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime")
   */
  private $createdAt;

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId(int $id)
  {
    $this->id = $id;
  }

  /**
   * @return Order
   */
  public function getOrder()
  {
    return $this->order;
  }

  /**
   * @param Order $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }

  /**
   * @return OrderStatus
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * @param OrderStatus $status
   */
  public function setStatus(OrderStatus $status)
  {
    $this->status = $status;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  /**
   * @param \DateTime $createdAt
   */
  public function setCreatedAt(\DateTime $createdAt)
  {
    $this->createdAt = $createdAt;
  }

  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }

  /**
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }

  /**
   * @return User|null
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }

  /**
   * @ORM\PreUpdate()
   * @ORM\PrePersist()
   */
  public function setCreatedAtPreChange()
  {
    $this->setCreatedAt(new \DateTime('now'));
  }

}