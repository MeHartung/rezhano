<?php

namespace StoreBundle\EventListener;

use Accurateweb\MoyskladIntegrationBundle\Event\MoyskladOrderCreateEvent;
use Doctrine\Common\Persistence\ObjectManager;

class MoyskladOrderCreateListener
{
  private $entityManager;

  public function __construct (ObjectManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function onMoyskladOrderCreate(MoyskladOrderCreateEvent $event)
  {
    $order = $event->getOrder();
    $order->setMoyskladSent(true);
    $this->entityManager->persist($order);
    $this->entityManager->flush();
  }
}