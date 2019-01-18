<?php

namespace StoreBundle\Service\Order;

use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Service\Product\ProductPrice\ProductPriceManager;

class TotalCalculator
{
  private $priceManager;

  public function __construct (ProductPriceManager $priceManager)
  {
    $this->priceManager = $priceManager;
  }

  /**
   * @param Order $order
   */
  public function calculate(Order $order)
  {
    /*
     * Рассчитываем стоимость заказа в зависимости от его текущего состояния
     * Пересчет стоимости товаров разерешен только на этапе формирования корзины
     * На следующих шагов только пересчитывается общая стоимость заказа, т.к. включает стоимость доставки
     * После оформления стомость заказа не изменяется
     */
    $checkoutStateId = $order->getCheckoutStateId();

    switch ($checkoutStateId)
    {
      case Order::CHECKOUT_STATE_CART:
      case Order::CHECKOUT_STATE_DELIVERY:
      case Order::CHECKOUT_STATE_PAYMENT:
        $this
          ->calculateSubtotal($order)
          ->calculateOrderDiscount($order)
          ->calculateTotal($order);
        break;
      case Order::CHECKOUT_STATE_COMPLETE:
      case Order::CHECKOUT_STATE_CART_CHECKOUT:
      default:
        break;
    }

    if ($order->getSubtotal() === null)
    {
      $this->calculateSubtotal($order);
    }

    if ($order->getTotal() === null)
    {
      $this->calculateTotal($order);
    }
  }

  /**
   * @param Order $order
   * @return $this
   */
  private function calculateTotal(Order $order)
  {
    $total = $order->getSubtotal()
      + $order->getShippingCost()
      + $order->getFee();

    $order->setTotal((float)$total);

    return $this;
  }

  /**
   * @param Order $order
   * @return $this
   */
  private function calculateSubtotal(Order $order)
  {
    $items = $order->getOrderItems();
    $total = 0;

    foreach ($items as $item)
    {
      $itemPrice = $item->getPrice();

      if ($item->getProduct())
      {
        $itemPrice = $item->getProduct()->getUnitPrice();
        #$itemPrice = $this->priceManager->getProductPrice($item->getProduct());
      }

      $total += $itemPrice * $item->getQuantity();
      $item->setPrice($itemPrice);
    }

    $order->setSubtotal($total);

    return $this;
  }

  /**
   * @param Order $order
   * @return $this
   */
  private function calculateOrderDiscount(Order $order)
  {
    $items = $order->getOrderItems();
    $total = 0;

    foreach ($items as $item)
    {
      if ($item->getProduct())
      {
        $total += $item->getProduct()->getUnitPrice() * $item->getQuantity();
        #$total += $this->priceManager->getProductPriceDiff($item->getProduct());
      }
    }

    $order->setDiscountSum($total);

    return $this;
  }
}