<?php

namespace StoreBundle\EventListener\EmailMessaging;

use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
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
    SettingManagerInterface $settingManager
  )
  {
    $this->mailer = $mailer;
    $this->emailFactory = $emailFactory;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->logger = $logger;
    $this->settingManager = $settingManager;
  }

  public function onCheckout(OrderCheckoutEvent $event)
  {
    $order = $event->getOrder();
    $operatorEmail = $this->settingManager->getValue('operator_email');

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