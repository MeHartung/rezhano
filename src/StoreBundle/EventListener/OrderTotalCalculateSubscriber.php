<?php

namespace StoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Service\Order\TotalCalculator;

/*
 * Пересчитываем стоимость order и orderitem при сохранении
 */
class OrderTotalCalculateSubscriber implements EventSubscriber
{
  private $calculator;

  public function __construct (TotalCalculator $calculator)
  {
    $this->calculator = $calculator;
  }

  public function getSubscribedEvents ()
  {
    /*
     * Изменение в PreUpdate ломало определение изменений в данных и данные не обновлялись в бд
     */
    return [
      'prePersist',
      //      'preUpdate',
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

      if ($order->getOrderItems()->contains($subject))
      {
        $order->getOrderItems()->removeElement($subject);
      }

      if ($order)
      {
        $total = $order->getTotal();
        $subTotal = $order->getSubtotal();
        $discount = $order->getDiscountSum();
        $this->calculator->calculate($order);

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
        $this->calculator->calculate($order);

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
        $this->calculator->calculate($order);

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
      $this->calculator->calculate($subject);
    }
    //    elseif ($subject instanceof OrderItem)
    //    {
    //      $order = $subject->getOrder();
    //
    //      if ($order)
    //      {
    //        $em = $args->getObjectManager();
    //        $total = $order->getTotal();
    //        $subTotal = $order->getSubtotal();
    //        $discount = $order->getDiscountSum();
    //        $this->calculator->calculate($order);
    //
    //        if (($total !== $order->getTotal() || $subTotal !== $order->getSubtotal() || $discount !== $order->getDiscountSum())
    //          && $order->getId()
    //        )
    //        {
    //          $em->persist($order);
    //          $em->flush();
    //        }
    //      }
    //    }
  }

  public function preUpdate(LifecycleEventArgs $args)
  {
    $subject = $args->getObject();

    if ($subject instanceof Order)
    {
      $this->calculator->calculate($subject);
    }
    //    elseif ($subject instanceof OrderItem)
    //    {
    //      $order = $subject->getOrder();
    //
    //      if ($order)
    //      {
    //        $em = $args->getObjectManager();
    //        $total = $order->getTotal();
    //        $subTotal = $order->getSubtotal();
    //        $discount = $order->getDiscountSum();
    //        $this->calculator->calculate($order);
    //
    //        if (($total !== $order->getTotal() || $subTotal !== $order->getSubtotal() || $discount !== $order->getDiscountSum())
    //          && $order->getId()
    //        )
    //        {
    //          $em->persist($order);
    //          $em->flush();
    //        }
    //      }
    //    }
  }
}