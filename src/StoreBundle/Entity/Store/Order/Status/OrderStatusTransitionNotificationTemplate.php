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
 * Time: 20:21
 */

namespace StoreBundle\Entity\Store\Order\Status;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="order_status_notification_templates")
 */
class OrderStatusTransitionNotificationTemplate
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
   * @ORM\Column(type="string", name="title", length=128)
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $subject;

  /**
   * @var string
   *
   * @ORM\Column(type="text")
   */
  private $body;

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
  public function getSubject()
  {
    return $this->subject;
  }

  /**
   * @param string $subject
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }

  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle(string $title)
  {
    $this->title = $title;
  }

  public function __toString()
  {
    return (string)$this->getTitle();
  }

}