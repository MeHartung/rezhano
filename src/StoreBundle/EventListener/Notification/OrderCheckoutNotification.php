<?php

namespace StoreBundle\EventListener\Notification;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Notification\OrderNotification;
use StoreBundle\Entity\Store\Order\Order;

class OrderCheckoutNotification
{
  protected $twig;

  public function __construct (EntityManager $entityManager, \Twig_Environment $twig)
  {
    $this->entityManager = $entityManager;
    $this->twig = $twig;
  }

  public function onCheckout(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();
    $notification = new OrderNotification();
    $notification->setOrder($order);
    $notification->setTitle(sprintf('Новый заказ №%s', $order->getDocumentNumber()));
    $notification->setMessage($this->getMessage($order));
    $notification->setUser($order->getUser());
    $this->entityManager->persist($notification);
    $this->entityManager->flush();
  }

  /**
   * @param Order $order
   * @return string
   */
  private function getMessage(Order $order)
  {
    return $this->twig->render('@Store/Common/order_notification.html.twig', [
      'order' => $order,
    ]);
  }
}