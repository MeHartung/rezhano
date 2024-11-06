<?php

namespace StoreBundle\EventListener\Ranking;


use Doctrine\ORM\EntityManager;
use StoreBundle\Event\FavoriteProductEvent;

class ProductFavoriteRankAdd
{
  private $entityManager;

  public function __construct (EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function onFavoriteAdd(FavoriteProductEvent $event)
  {
    $product = $event->getProduct();
    $productRank = $product->getProductRank();
    $productRank->setNbFavorites($productRank->getNbFavorites() + 1);
    $this->entityManager->persist($productRank);
    $this->entityManager->flush();
  }
}