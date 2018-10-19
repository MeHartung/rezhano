<?php

namespace StoreBundle\EventListener\EmailMessaging;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use Psr\Log\LoggerInterface;

class OrderCheckoutOperatorMail
{
  private $mailer;
  private $emailFactory;
  private $mailerFrom;
  private $mailerSenderName;
  private $operatorEmail;
  private $logger;

  /**
   * OrderCheckoutOperatorMail constructor.
   * @param \Swift_Mailer $mailer
   * @param EmailFactory $emailFactory
   * @param $mailerFrom string
   * @param $mailerSenderName string
   * @param $operatorEmail string
   * @param LoggerInterface $logger
   */
  public function __construct (\Swift_Mailer $mailer, EmailFactory $emailFactory, $mailerFrom, $mailerSenderName, $operatorEmail, LoggerInterface $logger)
  {
    $this->mailer = $mailer;
    $this->emailFactory = $emailFactory;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->operatorEmail = $operatorEmail;
    $this->logger = $logger;
  }

  public function onCheckout(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();

    if (!$this->operatorEmail)
    {
      $this->logger->error(sprintf('Unable to send email for checkout event: "Operator mail is required"'));
      return;
    }

    try
    {
      $email = $this->emailFactory->createMessage(
        'checkout_operator', //template
        array($this->mailerFrom => $this->mailerSenderName), //from
        array($this->operatorEmail), //to
        array( //variables
          'customer_name' => $order->getCustomerFullName(),
          'order_number' => $order->getDocumentNumber()
        ));

      $this->mailer->send($email);
    }
    catch (\Swift_TransportException $e)
    {
      $this->logger->error(sprintf('Unable to send email for checkout event: "%s"', $e->getMessage()));
    }
  }
}