<?php

namespace StoreBundle\EventListener\EmailMessaging;

use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use Psr\Log\LoggerInterface;
use StoreBundle\Event\CustomerQuestionEvent;

class CustomerQuestionCreateMail
{
  /**
   * CustomerQuestionCreateMail constructor.
   * @param \Swift_Mailer $mailer
   * @param EmailFactory $emailFactory
   * @param string $mailerFrom
   * @param string $mailerSenderName
   * @param string $operatorEmail
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

  /*
   * Срабатывает, когда пользователь создает новый вопрос
   */
  public function onCreate (CustomerQuestionEvent $event)
  {
    $customerQuestion = $event->getCustomerQuestion();

    if (!$this->operatorEmail)
    {
      $this->logger->error(sprintf('Unable to send email for customerQuestion create event: "Operator mail is required"'));
      return;
    }

    try
    {
      $email = $this->emailFactory->createMessage(
        'customer_question_operator',
        array($this->mailerFrom => $this->mailerSenderName),
        array($this->operatorEmail),
        array(
          'fio' => $customerQuestion->getUserName(),
          'question' => $customerQuestion->getMessage(),
          'date' => $customerQuestion->getCreatedAt()?$customerQuestion->getCreatedAt()->format('d.m.Y'):'',
        ));

      $this->mailer->send($email);
    }
    catch (\Swift_TransportException $e)
    {
      $this->logger->error(sprintf('Unable to send email for customerQuestion create event: "%s"', $e->getMessage()));
    }
  }

  /*
   * Срабатывает, когда пользователь отвечает на сообщение
   */
  public function onNewMessage(CustomerQuestionEvent $event)
  {
    $this->onCreate($event);
  }
}