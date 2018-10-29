<?php

namespace StoreBundle\Service\Order;


use Accurateweb\LogisticBundle\Service\ProductStockManager\ProductStockManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Exception\Order\OrderException;

class CartToOrderConverter
{
  private $stockManager;

  public function __construct (ProductStockManagerInterface $stockManager)
  {
    $this->stockManager = $stockManager;
  }

  /**
   * @param Order $cart
   * @return array|Order[]
   * @throws OrderException
   */
  public function convertToOrders(Order $cart)
  {
    $items = $cart->getOrderItems();
    $orders = [];

    if (!$items || !count($items))
    {
      throw new \InvalidArgumentException('Корзина должа содердать товары');
    }

    foreach ($items as $item)
    {
      $warehouse = $this->stockManager->getAvailableWarehouse($item->getProduct());

      if (!$warehouse)
      {
        throw new OrderException(sprintf('Заказ %s имеет товары не привязанные к складу'));
      }

      if (!isset($orders[$warehouse->getId()]))
      {
        $orders[$warehouse->getId()] = $this->cloneCart($cart);
      }

      $orderItem = new OrderItem();
      $orderItem->setProduct($item->getProduct());
      $orderItem->setPrice($item->getPrice());
      $orderItem->setQuantity($item->getQuantity());
      $orders[$warehouse->getId()]->addOrderItem($orderItem);
    }
    
    return $orders;
  }

  /**
   * @param Order $cart
   * @return Order
   */
  private function cloneCart(Order $cart)
  {
    $order = new Order();
    $order->setShippingAddress($cart->getShippingAddress());
    $order->setCustomerFirstName($cart->getCustomerFirstName());
    $order->setPaymentMethod($cart->getPaymentMethod());
    $order->setOrderStatus($cart->getOrderStatus());
    $order->setSubtotal($cart->getSubtotal());
    $order->setTotal($cart->getTotal());
    $order->setShippingCost($cart->getShippingCost());
    $order->setFee($cart->getFee());
    $order->setDiscountSum($cart->getDiscountSum());
    $order->setCustomerLastName($cart->getCustomerLastName());
    $order->setCustomerPhone($cart->getCustomerPhone());
    $order->setCustomerEmail($cart->getCustomerEmail());
    $order->setCustomerComment($cart->getCustomerComment());
    $order->setShippingCityFiasAouid($cart->getShippingCityFiasAouid());
    $order->setShippingCityName($cart->getShippingCityName());
    $order->setShippingPostCode($cart->getShippingPostCode());
    $order->setShippingAddress($cart->getShippingAddress());
    $order->setShippingMethod($cart->getShippingMethod());
    #$order->setShippingMethod($cart->getShippingMethod());
    $order->setCheckoutStateId($cart->getCheckoutStateId());
    $order->setVirtuemartOrderId($cart->getVirtuemartOrderId());
    $order->setCreatedAt($cart->getCreatedAt());
    $order->setUpdatedAt($cart->getUpdatedAt());
    $order->setUser($cart->getUser());
    $order->setPaymentStatus($cart->getPaymentStatus());
//    $order->setPreoderDate($cart->getPreoderDate());
    $order->setShippingDate($cart->getShippingDate());
    
    return $order;
  }
}