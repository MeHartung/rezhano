<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 23.03.18
 * Time: 16:40
 */

namespace StoreBundle\EventListener;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use Accurateweb\SettingBundle\Model\Manager\SettingManager;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Setting;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatus;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;

class OrderCheckoutListener
{
  private $em, $settingService;

  public function __construct(EntityManagerInterface $em, SettingManager $service)
  {
    $this->em = $em;
    $this->settingService = $service;
  }

  public function onOrderCheckoutPre(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();
    $this->setDefaultVars($order);
  }

  /*
    * Устанавливаем дефолтные значения заказа
    */
  protected function setDefaultVars(Order $order)
  {
//    $defaultOrderStatusId = $this->settingService->getValue(Setting::SETTING_DEFAULT_ORDER_STATUS);
//    $orderStatus = $this->em->getRepository(OrderStatus::class)->find((int)$defaultOrderStatusId);

    $orderStatus = $this->settingService->getValue(Setting::SETTING_DEFAULT_ORDER_STATUS);

    if ($orderStatus)
    {
      $orderStatusHistory = new OrderStatusHistory();
      $orderStatusHistory->setStatus($orderStatus);
      $orderStatusHistory->setOrder($order);
      $this->em->persist($orderStatusHistory);
      $this->em->flush();

      $order->setOrderStatus($orderStatus);
    }

//    $defaultOrderPaymentStatusId = $this->settingService->getValue(Setting::SETTING_DEFAULT_ORDER_PAYMENT_STATUS);
//    $orderPaymentStatus = $this->em->getRepository(OrderPaymentStatus::class)->find((int)$defaultOrderPaymentStatusId);

    $orderPaymentStatus = $this->settingService->getValue(Setting::SETTING_DEFAULT_ORDER_PAYMENT_STATUS);

    if($orderPaymentStatus)
    {
      $order->setPaymentStatus($orderPaymentStatus);
    }

    $order->setCheckoutStateId(Order::CHECKOUT_STATE_COMPLETE);
    $order->setCheckoutAt(new \DateTime());
  }
}