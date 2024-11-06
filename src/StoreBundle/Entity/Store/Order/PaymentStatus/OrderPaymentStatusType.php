<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 13.03.18
 * Time: 15:57
 */

namespace StoreBundle\Entity\Store\Order\PaymentStatus;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderPaymentStatusType
 * @package StoreBundle\Entity\Store\Order\PaymentStatus
 * @ORM\Entity()
 * @ORM\Table(name="order_payment_status_types")
 */
class OrderPaymentStatusType
{
  const TYPE_PAID = 'fdc5232b-e0eb-4914-8748-3e7de4a585e8';
  const TYPE_NOT_PAID = 'a9213afe-5fec-4a72-9c08-e5bb5e86beb9';

  /**
   * @var integer
   * @ORM\Id()
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue()
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatus", mappedBy="type")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(type="string", name="name", length=124)
   */
  private $name;

  /**
   * @var string
   * @ORM\Column(type="string", name="guid", length=124)
   */
  private $guid;

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
   * @return mixed
   */
  public function getGuid()
  {
    return $this->guid;
  }

  /**
   * @param mixed $guid
   */
  public function setGuid($guid)
  {
    $this->guid = $guid;
  }

  public function __toString()
  {
    return (string)$this->getName();
  }
}