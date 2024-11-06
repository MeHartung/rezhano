<?php

namespace StoreBundle\EventListener\EmailMessaging;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use AccurateCommerce\Shipping\ShippingManager;
use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use Accurateweb\SettingBundle\Model\Manager\SettingManager;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use Psr\Log\LoggerInterface;

class OrderCheckoutOperatorMail
{
  private $mailer;
  private $emailFactory;
  private $mailerFrom;
  private $mailerSenderName;
  private $logger;
  private $settingManager;
  private $shippingManager;
  private $twig;

  /**
   * OrderCheckoutOperatorMail constructor.
   * @param \Swift_Mailer $mailer
   * @param EmailFactory $emailFactory
   * @param $mailerFrom string
   * @param $mailerSenderName string
   * @param LoggerInterface $logger
   */
  public function __construct (
    \Swift_Mailer $mailer,
    EmailFactory $emailFactory,
    $mailerFrom, $mailerSenderName,
    LoggerInterface $logger,
    SettingManagerInterface $settingManager,
    ShippingManager $shippingManager,
    \Twig_Environment $twig
  )
  {
    $this->mailer = $mailer;
    $this->emailFactory = $emailFactory;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->logger = $logger;
    $this->settingManager = $settingManager;
    $this->shippingManager = $shippingManager;
    $this->twig = $twig;
  }

  public function onCheckout(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();
    $operatorEmail = $this->settingManager->getValue('operator_email');
    $shippingMethod = $this->shippingManager->getShippingMethodByUid($order->getShippingMethod()->getUid());

    if (!$operatorEmail)
    {
      $this->logger->error(sprintf('Unable to send email for checkout event: "Operator mail is required"'));
      return;
    }

    try
    {
      $email = $this->emailFactory->createMessage(
        'checkout_operator', //template
        array($this->mailerFrom => $this->mailerSenderName), //from
        array($operatorEmail), //to
        array( //variables
          'customer_name' => $order->getCustomerFullName(),
          'order_number' => $order->getDocumentNumber(),
          'customer_phone' => $order->getCustomerPhone(),
          'customer_email' => $order->getCustomerEmail(),
          'payment_method' => $order->getPaymentMethod()->getName(),
          'shipping_method' => $shippingMethod->getName(),
          'shipping_address' => $order->getFullShippingAddress(),
          'subtotal' => $order->getSubtotal(),
          'shipping_cost' => $order->getShippingCost(),
          'fee' => $order->getFee(),
          'total' => $order->getTotal(),
          'date' => $order->getCheckoutAt()->format('d.m.Y H:i'),
          'customer_comment' => $order->getCustomerComment(),
          'social_items'   => $this->twig->render('@Store/Email/Checkout/social_items.html.twig'),
          'order_items' => $this->twig->render('@Store/Email/Checkout/order_items.html.twig', array(
            'items' => $order->getOrderItems()
          )),
        ));

      $this->mailer->send($email);
    }
    catch (\Swift_TransportException $e)
    {
      $this->logger->error(sprintf('Unable to send email for checkout event: "%s"', $e->getMessage()));
    }
  }
}