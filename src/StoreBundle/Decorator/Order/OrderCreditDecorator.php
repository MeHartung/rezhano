<?php

namespace StoreBundle\Decorator\Order;


use AccurateCommerce\Shipping\Method\ShippingMethod;
use Doctrine\Common\Collections\ArrayCollection;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;

/**
 * Class OrderCreditDecorator
 * Изменяет orderItems для заказа в кредит не изменяя сам заказ.
 * @package StoreBundle\Decorator\Order
 */
class OrderCreditDecorator implements OrderDecoratorInterface
{
  /** @var Order */
  private $order;
  
  public function __construct(Order $order)
  {
    $this->order = $order;
  }
  
  /**
   * В случае с заявкой на кредит в Тинкофф доставка должна входить в список покупок, если она платная.
   * @return ArrayCollection - список товаров в заказе
   */
  public function getOrderItems()
  {
    $orderItems = clone $this->order->getOrderItems();
    
    /** @var ShippingMethod $shippingMethod */
    $shippingMethod = $this->order->getShippingMethod();
    
    if((float)$this->order->getShippingCost() > 0)
    {
      $deliveryAsProduct = new Product();
  
      $deliveryAsProduct->setName($shippingMethod->getName($this->order->getShippingCityName()));
      $deliveryAsProduct->setPrice($this->order->getShippingCost());
      
      $orderItemDelivery = new OrderItem();
      $orderItemDelivery->setQuantity(1);
      $orderItemDelivery->setPrice($deliveryAsProduct->getPrice());
      $orderItemDelivery->setProduct($deliveryAsProduct);
  
      $orderItems->add($orderItemDelivery);
    }
  
    return $orderItems;
  }
}