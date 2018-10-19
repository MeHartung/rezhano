<?php

namespace StoreBundle\EventListener\Security;

use AppBundle\DataAdapter\Form\FormErrorAdapter;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use StoreBundle\Entity\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class RegistrationListener implements EventSubscriberInterface
{
  const REGISTER_ROUTE = 'fos_user_registration_register';

  private $formErrorAdapter;
  private $router;
  private $authorizationChecker;

  public function __construct (FormErrorAdapter $formErrorAdapter, RouterInterface $router, AuthorizationChecker $authorizationChecker)
  {
    $this->formErrorAdapter = $formErrorAdapter;
    $this->router = $router;
    $this->authorizationChecker = $authorizationChecker;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents ()
  {
    return array(
      FOSUserEvents::REGISTRATION_FAILURE => 'onRegistrationFailure',
      FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationCompleted',
      KernelEvents::REQUEST => 'onRequest',
    );
  }

  public function onRegistrationFailure (FormEvent $event)
  {
    if ($event->getRequest()->isXmlHttpRequest())
    {
      $form = $event->getForm();
      $event->setResponse(new JsonResponse([
        'errors' => $this->formErrorAdapter->transform($form),
      ], 400));
    }
  }

  public function onRegistrationCompleted (FormEvent $event)
  {
    if ($event->getRequest()->isXmlHttpRequest())
    {
      //$url = $this->router->generate('fos_user_registration_confirmed');

      //Так как Backbone.js ждет обновленные значения полей в ответ, мы должны вернуть пустой ответ,
      //чтобы он не потер внутри свои значения
      $response = new JsonResponse([]);
      $event->setResponse($response);
    }
    else
    {
      $response = new RedirectResponse($this->router->generate('homepage'), 301);
      $event->setResponse($response);
    }
  }

  public function onRequest (GetResponseEvent $event)
  {
    try
    {
      //Редиректим со страницы регистрации пользователя в профиль, если он авторизован
      if ($this->authorizationChecker->isGranted(User::ROLE_DEFAULT) && $event->getRequest()->get('_route') == self::REGISTER_ROUTE)
      {
        $event->setResponse(new RedirectResponse($this->router->generate('fos_user_profile_show'), 302));
      }
    }
    catch (AuthenticationCredentialsNotFoundException $e)
    {

    }
  }

}