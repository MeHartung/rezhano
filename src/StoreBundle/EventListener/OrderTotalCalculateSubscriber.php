<?php

namespace StoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\User\User;
use StoreBundle\Service\Product\ProductPrice\ProductPriceManager;

/*
 * Пересчитываем стоимость order и orderitem при сохранении
 */
class OrderTotalCalculateSubscriber implements EventSubscriber
{
  private $priceManager;

  public function __construct (ProductPriceManager $priceManager)
  {
    $this->priceManager = $priceManager;
  }

  public function getSubscribedEvents ()
  {
    return [
      'prePersist',
      'preUpdate',
      'postPersist',
      'postUpdate',
      'postRemove'
    ];
  }

  public function postRemove(LifecycleEventArgs $args)
  {
    $subject = $args->getObject();

    if ($subject instanceof OrderItem)
    {
      $em = $args->getObjectManager();
      $order = $subject->getOrder();

      if ($order)
      {
        $total = $order->getTotal();
        $subTotal = $order->getSubtotal();
        $discount = $order->getDiscountSum();
        $this->updateOrderPrices($order);

        if ($total !== $order->getTotal() || $subTotal !== $order->getSubtotal() || $discount !== $order->getDiscountSum())
        {
          $em->persist($order);
          $em->flush();
        }
      }
    }
  }

  public function postUpdate(LifecycleEventArgs $args)
  {
    $subject = $args->getObject();

    if ($subject instanceof OrderItem)
    {
      $em = $args->getObjectManager();
      $order = $subject->getOrder();

      if ($order)
      {
        $total = $order->getTotal();
        $subTotal = $order->getSubtotal();
        $discount = $order->getDiscountSum();
        $this->updateOrderPrices($order);

        if ($total !== $order->getTotal() || $subTotal !== $order->getSubtotal() || $discount !== $order->getDiscountSum())
        {
          $em->persist($order);
          $em->flush();
        }
      }
    }
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $subject = $args->getObject();

    if ($subject instanceof OrderItem)
    {
      $em = $args->getObjectManager();
      $order = $subject->getOrder();

      if ($order)
      {
        $total = $order->getTotal();
        $subTotal = $order->getSubtotal();
        $discount = $order->getDiscountSum();
        $this->updateOrderPrices($order);

        if ($total !== $order->getTotal() || $subTotal !== $order->getSubtotal() || $discount !== $order->getDiscountSum())
        {
          $em->persist($order);
          $em->flush();
        }
      }
    }
  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $subject = $args->getObject();

    if ($subject instanceof Order)
    {
      $this->updateOrderPrices($subject);
    }
    elseif ($subject instanceof OrderItem)
    {
      $order = $subject->getOrder();

      if ($order && ($order->getCheckoutStateId() < Order::CHECKOUT_STATE_COMPLETE) && $subject->getProduct())
      {
        $subject->setPrice($subject->getProduct()->getUnitPrice());
        #$subject->setPrice($this->priceManager->getProductPrice($subject->getProduct()));
      }
    }
  }

  public function preUpdate(LifecycleEventArgs $args)
  {
    $subject = $args->getObject();

    if ($subject instanceof Order)
    {
      $this->updateOrderPrices($subject);
    }
    elseif ($subject instanceof OrderItem)
    {
      $order = $subject->getOrder();

      if ($order && ($order->getCheckoutStateId() < Order::CHECKOUT_STATE_COMPLETE) && $subject->getProduct())
      {
        $subject->setPrice($subject->getProduct()->getUnitPrice());
        #$subject->setPrice($this->priceManager->getProductPrice($subject->getProduct()));
      }
    }
  }

  protected function updateOrderPrices(Order $order)
  {
    $order->setTotal($this->calculateOrderTotal($order));
    $order->setSubtotal($this->calculateOrderSubTotal($order));
    $order->setDiscountSum($this->calculateOrderDiscount($order));
  }

  /**
   * Сумма скидки
   * @param Order $order
   * @return float|int
   */
  private function calculateOrderDiscount(Order $order)
  {
    $items = $order->getOrderItems();
    $total = 0;

    foreach ($items as $item)
    {
      $total += $this->priceManager->getProductPriceDiff($item->getProduct());
    }

    return $total;
  }

  /**
   * Стоимость заказа
   * @param Order $order
   * @return float
   */
  private function calculateOrderTotal (Order $order)
  {
    return $this->calculateOrderSubTotal($order)
      + $order->getShippingCost()
      + $order->getFee();
  }

  /**
   * Стоимость товаров
   * @param Order $order
   * @return float
   */
  private function calculateOrderSubTotal(Order $order)
  {
    $items = $order->getOrderItems();
    $total = 0;

    foreach ($items as $item)
    {
      $total += $item->getProduct()->getUnitPrice() * $item->getQuantity();
      #$total += $this->priceManager->getProductPrice($item->getProduct()) * $item->getQuantity();
    }

    return $total;
  }
}