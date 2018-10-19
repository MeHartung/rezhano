<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Mailer;

use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

/**
 * Кастомный мейлер, который отсылает письма по шаблону
 * @package StoreBundle\Mailer
 */
class FOSUserMailer implements MailerInterface
{
  private $router,
          $emailFactory,
          $mailerFrom,
          $mailerSenderName,
          $mailer,
          $logger
  ;

  public function __construct(Router $router, EmailFactory $emailFactory,
    \Swift_Mailer $mailer, Logger $logger, $mailerFrom, $mailerSenderName)
  {
    $this->router = $router;
    $this->emailFactory = $emailFactory;
    $this->mailer = $mailer;
    $this->mailerFrom = $mailerFrom;
    $this->mailerSenderName = $mailerSenderName;
    $this->logger = $logger;
  }

  public function sendConfirmationEmailMessage(UserInterface $user)
  {
    $url = $this->router->generate('fos_user_registration_confirm',
      array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);

    $context = array(
      'username' => $user->getUsername(),
      'confirmation_url' => $url,
    );

    $this->sendMessage('user_registration_confirm', $context, (string) $user->getEmail());
  }

  public function sendResettingEmailMessage(UserInterface $user)
  {
    $url = $this->router->generate('fos_user_resetting_reset',
      array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);

    $context = array(
      'username' => $user->getUsername(),
      'confirmation_url' => $url,
    );

    $this->sendMessage('user_password_reset_request', $context, (string) $user->getEmail());
  }

  public function sendResettingResetCompletedMessage(UserInterface $user)
  {
    $context = array(
      'username' => $user->getUsername(),
    );

    $this->sendMessage('user_password_reset_completed', $context, (string) $user->getEmail());
  }

  protected function sendMessage($template, $variables, $to)
  {
    $email = $this->emailFactory->createMessage(
      $template,
      [$this->mailerFrom => $this->mailerSenderName],
      $to,
      $variables
    );

    $this->mailer->send($email);
  }
}
