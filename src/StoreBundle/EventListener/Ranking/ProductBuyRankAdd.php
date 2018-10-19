<?php

namespace StoreBundle\EventListener\Ranking;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use Doctrine\ORM\EntityManager;

class ProductBuyRankAdd
{
  private $entityManager;

  public function __construct (EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function onCheckout(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();
    $items = $order->getOrderItems();

    foreach ($items as $item)
    {
      $product = $item->getProduct();

      if ($product)
      {
        $productRank = $product->getProductRank();
        $productRank->setNbBuy($productRank->getNbBuy() + 1);
        $this->entityManager->persist($productRank);
        $this->entityManager->flush();
      }
    }
  }
}