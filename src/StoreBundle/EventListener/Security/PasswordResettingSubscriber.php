<?php

namespace StoreBundle\EventListener\Security;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use StoreBundle\Entity\User\User;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use StoreBundle\Mailer\FOSUserMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class PasswordResettingSubscriber implements EventSubscriberInterface
{
  private $router;

  private $retryTtl;

  private $fosUserMailer;

  public function __construct(Router $router, $retryTtl, FOSUserMailer $userMailer)
  {
    $this->router = $router;
    $this->retryTtl = $retryTtl;
    $this->fosUserMailer = $userMailer;
  }

  public static function getSubscribedEvents()
  {
    return [
      FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE => 'onResettingSendEmailInitialize',
      FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED => 'onResettingSendEmailCompleted',
//      FOSUserEvents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess',
      FOSUserEvents::RESETTING_RESET_COMPLETED => 'onResettingResetCompleted'
    ];
  }


  public function onResettingSendEmailCompleted(GetResponseUserEvent $event)
  {
    $request = $event->getRequest();

    if ($request->isXmlHttpRequest())
    {
      $event->setResponse(new JsonResponse(['success' => true]));
    }

    return new RedirectResponse($this->router->generate('fos_user_resetting_check_email',
      array('username' => $event->getUser()->getUsername())));
  }

  /**
   * @param GetResponseNullableUserEvent $event
   */
  public function onResettingSendEmailInitialize(GetResponseNullableUserEvent $event)
  {
    $request = $event->getRequest();

    $user = $event->getUser();

    if (!$user instanceof User)
    {
      $message = 'Пользователь с указанной электронной почтой не существует';

      if ($event->getRequest()->isXmlHttpRequest())
      {
        $response = new JsonResponse(['error' => $message], 400);
      }
      else
      {
        $event->getRequest()->getSession()->getFlashBag()->add('error', $message);
        $response = new RedirectResponse($this->router->generate('fos_user_resetting_request'));
      }

      $event->setResponse($response);
    }
    else
    {
      if ($user->isPasswordRequestNonExpired($this->retryTtl))
      {
        if ($request->isXmlHttpRequest())
        {
          $event->setResponse(new JsonResponse(['error' => 'Ссылка для восстановления пароля уже была выслана на Ваш e-mail. Пожалуйста, повторите позднее.'], 400));
        }
      }
    }
  }

  public function onResettingResetCompleted(FilterUserResponseEvent $event)
  {
    $this->fosUserMailer->sendResettingResetCompletedMessage($event->getUser());
  }
}