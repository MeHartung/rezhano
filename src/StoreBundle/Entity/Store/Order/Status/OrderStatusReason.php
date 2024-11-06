<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 01.02.18
 * Time: 11:26
 */

namespace StoreBundle\Entity\Store\Order\Status;

use Doctrine\ORM\Mapping as ORM;

/**
 * Шаблоны для причин отмены заказа
 * @ORM\Entity()
 * @ORM\Table(name="order_status_reasons")
 */
class OrderStatusReason
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
   * @var string|null
   *
   * @ORM\Column(type="text")
   */
  private $text;

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
   * @return null|string
   */
  public function getText()
  {
    return $this->text;
  }

  /**
   * @param null|string $text
   */
  public function setText(string $text)
  {
    $this->text = $text;
  }

  public function __toString()
  { #вернём первые 50 символов, если длина примечания более 50 символов
    return (string)strlen($this->getText()) > 50 ? mb_substr($this->getText(), 0, 50, 'UTF-8') : $this->getText();
  }
}