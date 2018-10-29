<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 26.10.2018
 * Time: 18:51
 */

namespace StoreBundle\Entity\Integration;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use StoreBundle\Entity\Store\Order\Order;

/**
 * Class MoyskladQueue
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Integration\MoyskladQueueRepository")
 */
class MoyskladQueue
{
  /**
   * @var integer
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;
  /**
   * @var Order
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Store\Order\Order", inversedBy="moysklad_queue", cascade={"remove", "persist"})
   * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
   */
  private $order;
  
  /**
   * @var \DateTime
   * @ORM\Column(type="datetime", nullable=true)
   * @Gedmo\Timestampable(on="create")
   */
  private $created_at;
  
  /**
   * @var \DateTime
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $sent_at;
  
  /**
   * @var string
   * @ORM\Column(type="text", nullable=true)
   */
  private $message;
  
  /**
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }
  
  /**
   * @return Order
   */
  public function getOrder ()
  {
    return $this->order;
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
   * @return \DateTime
   */
  public function getCreatedAt ()
  {
    return $this->created_at;
  }
  
  /**
   * @param \DateTime $created_at
   * @return $this
   */
  public function setCreatedAt ($created_at)
  {
    $this->created_at = $created_at;
    return $this;
  }
  
  /**
   * @return \DateTime
   */
  public function getSentAt ()
  {
    return $this->sent_at;
  }
  
  /**
   * @param \DateTime $sent_at
   * @return $this
   */
  public function setSentAt ($sent_at)
  {
    $this->sent_at = $sent_at;
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
  
}