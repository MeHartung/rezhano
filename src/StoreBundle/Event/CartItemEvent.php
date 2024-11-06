<?php

namespace StoreBundle\Event;


use StoreBundle\Entity\Store\Order\OrderItem;
use Symfony\Component\EventDispatcher\Event;

class CartItemEvent extends Event
{
  private $orderItem;

  public function __construct (OrderItem $orderItem)
  {
    $this->orderItem = $orderItem;
  }

  /**
   * @return OrderItem
   */
  public function getOrderItem (): OrderItem
  {
    return $this->orderItem;
  }
}