<?php

namespace StoreBundle\Entity\Store\Order\PaymentStatus;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderPaymentStatus
 * @package StoreBundle\Entity\Store\Order\PaymentStatus
 * @ORM\Entity()
 * @ORM\Table(name="order_payment_statuses")
 */
class OrderPaymentStatus
{
  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Order\Order", mappedBy="paymentStatus")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $name;

  /**
   * @var OrderPaymentStatusType
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatusType")
   */
  private $type;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return OrderPaymentStatusType
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param OrderPaymentStatusType $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  public function isPaid()
  {
    if($this->getType()->getGuid() === OrderPaymentStatusType::TYPE_PAID)
    {
      return true;
    }
    return false;
  }

  public function __toString()
  {
    return (string)$this->getName();
  }
}