<?php

namespace StoreBundle\EventListener\Ranking;


use Doctrine\ORM\EntityManager;
use StoreBundle\Event\CartItemEvent;

class CartItemAddRankAdd
{
  private $entityManager;

  public function __construct (EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function onAdd(CartItemEvent $event)
  {
    $cartItem = $event->getOrderItem();
    $product = $cartItem->getProduct();

    if ($product)
    {
      $productRank = $product->getProductRank();
      $productRank->setNbCart($productRank->getNbCart() + 1);
      $this->entityManager->persist($productRank);
      $this->entityManager->flush();
    }
  }
}