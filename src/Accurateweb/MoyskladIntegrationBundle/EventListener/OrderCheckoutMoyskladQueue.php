<?php

namespace Accurateweb\MoyskladIntegrationBundle\EventListener;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use AppBundle\Entity\Store\Integration\MoyskladQueue;
use Doctrine\Common\Persistence\ObjectManager;

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