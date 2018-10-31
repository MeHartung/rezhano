<?php

namespace Accurateweb\MoyskladIntegrationBundle\EventListener;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Integration\MoyskladQueue;

class OrderCheckoutMoyskladQueue
{
  private $entityManager;

  public function __construct (ObjectManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function onOrderCheckout(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();
    $queue = new MoyskladQueue();
    $queue->setOrder($order);

    $this->entityManager->persist($queue);
    $this->entityManager->flush();
  }
}