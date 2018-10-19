<?php

namespace StoreBundle\EventListener;


use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use Accurateweb\LogisticBundle\Service\ProductStockManager\ProductStockManagerInterface;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Logistics\Warehouse\ProductStock;

class OrderCheckoutProductStockReserve
{
  private $stockManager;
  private $entityManager;
  private $logger;

  public function __construct (ProductStockManagerInterface $stockManager, EntityManager $entityManager, LoggerInterface $logger)
  {
    $this->stockManager = $stockManager;
    $this->entityManager = $entityManager;
    $this->logger = $logger;
  }

  public function onCheckout(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();
    $items = $order->getOrderItems();

    foreach ($items as $item)
    {
      /** @var Product $product */
      $product = $item->getProduct();
      $quantity = $item->getQuantity();

      $warehouse = $this->stockManager->getAvailableWarehouse($product);

      if (!$warehouse)
      {
        $this->logger->error(sprintf('Не найден склад для товара %s', $product->getName()));
      }

      $currentStock = null;

      foreach ($product->getStocks() as $stock)
      {
        if ($stock->getWarehouse()->getId() === $warehouse->getId())
        {
          $currentStock = $stock;
        }
      }

      if (!$currentStock)
      {
        $this->logger->warning(sprintf('Резервируем товар %s, которого нет на складе %s', $product->getName(), $warehouse->getName()));
        $currentStock = new ProductStock();
        $currentStock->setProduct($product);
      }

      $currentStock->setReservedValue($quantity + $currentStock->getReservedValue());

      if ($currentStock->getReservedValue() > $currentStock->getValue())
      {
        $this->logger->warning(sprintf('Зарезервировали товара %s больше, чем имеем на складе %s', $product->getName(), $warehouse->getName()));
      }

      $this->entityManager->persist($currentStock);
      $this->entityManager->flush();
    }
  }
}