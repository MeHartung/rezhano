<?php

namespace Accurateweb\MoyskladIntegrationBundle\Event;

use StoreBundle\Entity\Store\Order\Order;
use MoySklad\Entities\Documents\Orders\CustomerOrder;
use Symfony\Component\EventDispatcher\Event;

class MoyskladOrderCreateEvent extends Event
{
  private $customerOrder;

  private $order;

  public function __construct (CustomerOrder $customerOrder, Order $order)
  {
    $this->customerOrder = $customerOrder;
    $this->order = $order;
  }

  /**
   * @return CustomerOrder
   */
  public function getCustomerOrder ()
  {
    return $this->customerOrder;
  }

  /**
   * @return Order
   */
  public function getOrder ()
  {
    return $this->order;
  }
}