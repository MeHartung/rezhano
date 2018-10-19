<?php

namespace StoreBundle\EventListener\EmailMessaging;


use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use Psr\Log\LoggerInterface;
use StoreBundle\Event\CustomerQuestionEvent;

class CustomerQuestionAnswer
{
  private $mailer;
  private $emailFactory;
  private $mailerFrom;
  private $mailerSenderName;
  private $logger;

  /**
   * CustomerQuestionCreateMail constructor.
   * @param \Swift_Mailer $mailer
   * @param EmailFactory $emailFactory
   * @param string $mailerFrom
   * @param string $mailerSenderName
   * @param string $operatorEmail
   * @param LoggerInterface $logger
   */
  public function __construct (\Swift_Mailer $mailer, EmailFactory $emailFactory, $mailerFrom, $mailerSenderName, LoggerInterface $logger)
  {
    $this->mailer = $mailer;
    $this->emailFactory = $emailFactory;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->logger = $logger;
  }

  public function onAnswer(CustomerQuestionEvent $event)
  {
    $answer = $event->getCustomerQuestion();
    $dialog = $answer->getDialog();
    $messages = $dialog->getMessages();
    /*
     * Вопрос - первое сообщение в диалоге
     */
    $question = $messages[0];

    if ($dialog->getCreator())
    {
      foreach ($messages as $message)
      {
        /*
         * Либо последнее сообщение, созданное пользователем
         */
        if ($message->getUser() && $message->getUser()->getId() == $dialog->getCreator()->getId())
        {
          $question = $message;
        }
      }
    }

    $email = $question->getUserEmail();

    if (!$email)
    {
      $this->logger->error(sprintf('Failed to send message for user %s. Email required', $answer->getUserName()));
      return;
    }

    $message = $this->emailFactory->createMessage(
      'customer_question_answer',
      array($this->mailerFrom => $this->mailerSenderName),
      array($email => $answer->getUserName()),
      [
      'question' => $question->getMessage(),
      'date_create' => $question->getCreatedAt()->format('d.m.Y H:i'),
      'message' => $answer->getMessage(),
      'date_answer' => $answer->getCreatedAt()?$answer->getCreatedAt()->format('d.m.Y H:i'):date('d.m.Y H:i'),
      'fio' => $answer->getUserName(),
      'email' => $email,
    ]);

    try
    {
      $this->mailer->send($message);
    }
    catch (\Exception $e)
    {
      $this->logger->error(sprintf('Failed to send message to %s. %s', $email, $e->getMessage()));
    }
  }
}