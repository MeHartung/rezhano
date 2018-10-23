<?php
/**
 * (c) 2017 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 * Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 * (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 * Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 * ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 * как нарушение его авторских прав.
 *
 * Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.10.2017
 * Time: 20:13
 */

namespace StoreBundle\Entity\Store\Order\Status;

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Validator\Constraints as StoreAssert;


/**
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Order\Status\OrderStatusRepository")
 * @ORM\Table(name="order_statuses")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderStatus
{
  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $name;

  /**
   * @var boolean
   *
   * @ORM\Column(type="boolean")
   */
  private $sendNotification = false;

  /**
   * @var OrderStatusType
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\Status\OrderStatusType", inversedBy="id")
   */
  private $type;

  /**
   * @var OrderStatusTransitionNotificationTemplate
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\Status\OrderStatusTransitionNotificationTemplate",
   *   cascade={"persist"})
   * @ORM\JoinColumn(name="transition_notification_template_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
   */
  private $notificationTemplate;

  /**
   * @var OrderStatusHistory
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Order\Status\OrderStatusHistory", mappedBy="status")
   */
  private $orderOrderStatus;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
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
   * @return OrderStatusTransitionNotificationTemplate
   */
  public function getNotificationTemplate()
  {
    return $this->notificationTemplate;
  }

  /**
   * @param OrderStatusTransitionNotificationTemplate $notificationTemplate
   */
  public function setNotificationTemplate($notificationTemplate)
  {
    $this->notificationTemplate = $notificationTemplate;
  }

  /**
   * @return bool
   */
  public function getSendNotification()
  {
    return $this->sendNotification;
  }

  /**
   * @param bool $sendNotification
   */
  public function setSendNotification($sendNotification)
  {
    $this->sendNotification = $sendNotification;
  }

  /**
   * @return null|string
   */
  public function getReason()
  {
    return $this->reason;
  }

  /**
   * @param null|string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }

  /**
   * @return OrderStatusType
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param OrderStatusType $type
   */
  public function setType(OrderStatusType $type)
  {
    $this->type = $type;
  }

  /**
   * @return OrderStatusHistory
   */
  public function getOrderOrderStatus(): OrderStatusHistory
  {
    return $this->orderOrderStatus;
  }

  /**
   * @param OrderStatusHistory $orderOrderStatus
   */
  public function setOrderOrderStatus(OrderStatusHistory $orderOrderStatus)
  {
    $this->orderOrderStatus = $orderOrderStatus;
  }


  public function __toString()
  {
    return (string)$this->getName();
  }

}