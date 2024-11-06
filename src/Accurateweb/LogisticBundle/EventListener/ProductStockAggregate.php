<?php

namespace Accurateweb\LogisticBundle\EventListener;

use Accurateweb\LogisticBundle\Model\ProductStockInterface;
use Accurateweb\LogisticBundle\Model\StockableInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

class ProductStockAggregate
{
  public function preUpdate(LifecycleEventArgs $event)
  {
    $productStock = $event->getObject();
    $em = $event->getObjectManager();

    if ($productStock instanceof ProductStockInterface)
    {
      $product = $productStock->getProduct();
      $this->calculateTotalStock($product, $em);
    }
  }

  public function postPersist(LifecycleEventArgs $event)
  {
    $productStock = $event->getObject();
    $em = $event->getObjectManager();

    if ($productStock instanceof ProductStockInterface)
    {
      $product = $productStock->getProduct();
      $this->calculateTotalStock($product, $em);
    }
  }

  private function calculateTotalStock(StockableInterface $product, ObjectManager $em)
  {
    $total = 0;
    $reserved = 0;
    $currentStock = $product->getTotalStock();
    $currentReserved = $product->getReservedStock();

    foreach ($product->getStocks() as $stock)
    {
      $total += $stock->getValue();
      $reserved += $stock->getReservedValue();
    }

    $product->setTotalStock($total);
    $product->setReservedStock($reserved);
    $product->setInStock($total > 0);

    if (($currentStock != $total) || ($currentReserved != $reserved))
    {
      $em->persist($product);
      $em->flush();
    }
  }
}