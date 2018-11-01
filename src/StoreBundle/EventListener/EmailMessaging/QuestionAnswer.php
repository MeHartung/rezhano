<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 01.11.18
 * Time: 20:41
 */

namespace StoreBundle\EventListener\EmailMessaging;


use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use Psr\Log\LoggerInterface;
use StoreBundle\Event\QuestionAnswerEvent;

class QuestionAnswer
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
  public function __construct (\Swift_Mailer $mailer, EmailFactory $emailFactory, $mailerFrom,
                               $mailerSenderName, $operatorEmail, LoggerInterface $logger)
  {
    $this->mailer = $mailer;
    $this->emailFactory = $emailFactory;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->operatorEmail = $operatorEmail;
    $this->logger = $logger;
  }
  
  public function onUpdate(QuestionAnswerEvent $event)
  {
    $question = $event->getQuestion();
    
    try
    {
      $email = $this->emailFactory->createMessage(
        'question_answer', //template
        array($this->mailerFrom => $this->mailerSenderName), //from
        array($question->getEmail()), //to
        array( //variables
          'customer_name' => $question->getFio(),
          'customer_email' => $question->getEmail(),
          'customer_phone' => $question->getPhone(),
          'question' => $question->getText()
        ));
      
      $this->mailer->send($email);
    }
    catch (\Swift_TransportException $e)
    {
      $this->logger->error(sprintf('Unable to send email for checkout event: "%s"', $e->getMessage()));
    }
  }
}
