<?php

namespace StoreBundle\EventListener\EmailMessaging;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use AccurateCommerce\Shipping\ShippingManager;
use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use Psr\Log\LoggerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use StoreBundle\Entity\Store\Order\Order;

class OrderCheckoutCustomerMail
{
  private $mailer;
  /**
   * @var EmailFactory
   */
  private $emailFactory;
  private $logger;
  private $mailerFrom;
  private $mailerSenderName;
  private $twig;

  /**
   * OrderCheckoutCustomerMail constructor.
   * @param \Swift_Mailer $mailer
   * @param LoggerInterface $logger
   * @param EmailFactory $emailFactory
   * @param ShippingManager $shippingManager
   * @param $mailerFrom string
   * @param $mailerSenderName string
   */
  public function __construct (\Swift_Mailer $mailer,
                               LoggerInterface $logger,
                               EmailFactory $emailFactory,
                               ShippingManager $shippingManager,
                               $mailerFrom,
                               $mailerSenderName,
                               \Twig_Environment $twig
  )
  {
    $this->mailer = $mailer;
    $this->logger = $logger;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->shippingManager = $shippingManager;
    $this->emailFactory = $emailFactory;
    $this->twig = $twig;
  }

  public function onCheckout (OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();

    if ($order->getCustomerEmail())
    {
      try
      {
        $email = $this->emailFactory->createMessage(
          'checkout',
          array($this->mailerFrom => $this->mailerSenderName),
          array($order->getCustomerEmail() => $order->getCustomerFullName()),
          $this->getEmailVariables($order)
        );

        $this->mailer->send($email);
      }
      catch (\Swift_TransportException $e)
      {
        $this->logger->error(sprintf('Unable to send email for checkout event: "%s"', $e->getMessage()));
      }
    }
  }

  private function getEmailVariables (Order $order)
  {
    $shippingMethod = $this->shippingManager->getShippingMethodByUid($order->getShippingMethodId()->getUid());

    $variables = array(
      'customer_name' => $order->getCustomerFullName(),
      'order_number' => $order->getDocumentNumber(),
      'customer_phone' => $order->getCustomerPhone(),
      'customer_email' => $order->getCustomerEmail(),
      'payment_method' => $order->getPaymentMethod()->getName(),
      'shipping_method' =>'',
      #'shipping_method' => $shippingMethod->getName(),
      'shipping_address' => $order->getFullShippingAddress(),
      'subtotal' => $order->getSubtotal(),
      'shipping_cost' => $order->getShippingCost(),
      'fee' => $order->getFee(),
      'total' => $order->getTotal(),
      'order_items' => $this->twig->render('@Store/Email/Checkout/order_items.html.twig', array(
        'items' => $order->getOrderItems()
      )),
    );

    return $variables;
  }
  
  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
  }
}