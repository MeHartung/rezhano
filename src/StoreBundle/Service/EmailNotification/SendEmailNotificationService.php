<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 01.11.17
 * Time: 14:55
 */

namespace StoreBundle\Service\EmailNotification;


use Accurateweb\EmailTemplateBundle\Template\EmailTemplate;
use Doctrine\ORM\EntityManagerInterface;
use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderStatusTransitionNotificationTemplate;
use Symfony\Bridge\Monolog\Logger;


class SendEmailNotificationService
{

  private $em;

  private $emailFactory;

  private $mailer;

  private $logger;

  private $mailerFrom,
          $mailerSenderName;

  private $twig;

  public function __construct(EmailFactory $emailFactory, \Swift_Mailer $mailer, Logger $logger, $mailerFrom,
    $mailerSenderName, EntityManagerInterface $em, \Twig_Environment $twig)
  {
    $this->emailFactory = $emailFactory;
    $this->mailer = $mailer;
    $this->logger = $logger;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->em = $em;
    $this->twig = $twig;
  }

  public function sendEmail(Order $order, $statusTemlateId)
  {
    /**
     * @var  $statusTemplate OrderStatusTransitionNotificationTemplate
     */
    $statusTemplate =  $this->em->createQuery('
                    SELECT a
                      FROM 
                        StoreBundle:Store\Order\Status\OrderStatusTransitionNotificationTemplate a
                      WHERE a.id = :id')
      ->setParameter('id', $statusTemlateId)
      ->getSingleResult();

    $template = new EmailTemplate(null, null, $statusTemplate->getSubject(), $statusTemplate->getBody());

    try
    {
      $email = $this->emailFactory->createMessage($template,
        array($this->mailerFrom => $this->mailerSenderName),
        array($order->getCustomerEmail() => $order->getCustomerFullName()),
        $this->getEmailVariables($order));

      $this->mailer->send($email);
    }
    catch (\Swift_TransportException $e)
    {
      $this->logger->error(sprintf('Unable to send email for checkout event: "%s"', $e->getMessage()));
    }
  }

  /**
   * @param Order $order
   * @return array
   */
  private function getEmailVariables($order)
  {

    $variables = array(
      'order_status' => ($order->getOrderStatus()) ? $order->getOrderStatus()->getName() : '',
      'customer_name' => $order->getCustomerFullName(),
      'order_number' => $order->getDocumentNumber(),
      'customer_phone' => $order->getCustomerPhone(),
      'customer_email'   => $order->getCustomerEmail(),
      'payment_method'   => $order->getPaymentMethod()->getName(),
      'shipping_method'   => $order->getShippingMethod()->getName(),
      'shipping_address'   => $order->getFullShippingAddress(),
      'subtotal'   => $order->getSubtotal(),
      'shipping_cost'   => $order->getShippingCost(),
      'fee'   => $order->getFee(),
      'total'   => $order->getTotal(),
      'social_items'   => $this->twig->render('@Store/Email/Checkout/social_items.html.twig'),
      'order_items'   => $this->twig->render('@Store/Email/Checkout/order_items.html.twig', array(
        'items' => $order->getOrderItems()
      ))
    );

    return $variables;
  }
}