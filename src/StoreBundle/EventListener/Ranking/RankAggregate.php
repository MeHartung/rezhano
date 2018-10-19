<?php

namespace StoreBundle\EventListener\Ranking;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use StoreBundle\Entity\Store\Catalog\Product\ProductRank;
use Symfony\Component\DependencyInjection\Container;

class RankAggregate
{
  private $container;

  /*
   * Здесь контейнер, т.к. у settingsManager зависимость от EntityManager
   * и это вызывает циклические зависимости
   */
  public function __construct (Container $serviceContainer)
  {
    $this->container = $serviceContainer;
  }

  public function postPersist (LifecycleEventArgs $event)
  {
    $productRank = $event->getObject();

    if ($productRank instanceof ProductRank)
    {
      $this->calculateRank($productRank, $event->getEntityManager());
    }
  }

  public function postUpdate (LifecycleEventArgs $event)
  {
    $productRank = $event->getObject();

    if ($productRank instanceof ProductRank)
    {
      $this->calculateRank($productRank, $event->getEntityManager());
    }
  }

  private function calculateRank(ProductRank $productRank, EntityManager $entityManager)
  {
    $product = $productRank->getProduct();
    $settingManager = $this->container->get('aw.settings.manager');

    $weightCart = $settingManager->getValue('rank_cart_weight');
    $weightFavorite = $settingManager->getValue('rank_favorite_weight');
    $weightView = $settingManager->getValue('rank_view_weight');
    $weightBuy = $settingManager->getValue('rank_buy_weight');

    $rank = $productRank->getNbBuy() * $weightBuy
      + $productRank->getNbCart() * $weightCart
      + $productRank->getNbFavorites() * $weightFavorite
      + $productRank->getNbViews() * $weightView;

    if ($product->getRank() !== $rank)
    {
      $product->setRank($rank);
      $entityManager->persist($product);
//      $entityManager->flush();
    }
  }
}