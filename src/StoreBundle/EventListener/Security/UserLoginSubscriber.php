<?php

namespace StoreBundle\EventListener\Security;

use StoreBundle\Entity\User\User;
use StoreBundle\Service\Order\CartService;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLoginSubscriber implements EventSubscriberInterface
{
  const AUTH_ROUTE = 'fos_user_security_login';
  /** @var AuthorizationChecker */
  private $authorization_checker;

  /** @var CartService */
  private $cart_service;

  /** @var Router $router */
  private $router;

  public function __construct (AuthorizationChecker $authorizationChecker, CartService $cartService, Router $router)
  {
    $this->authorization_checker = $authorizationChecker;
    $this->cart_service = $cartService;
    $this->router = $router;
  }

  public static function getSubscribedEvents()
  {
    return array(
      FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onLogin',
      SecurityEvents::INTERACTIVE_LOGIN => 'onLogin',
      KernelEvents::REQUEST => 'onRequest',
    );
  }

  public function onLogin($event)
  {
    if ($event instanceof UserEvent)
    {
      $user = $event->getUser();
    }

    if ($event instanceof InteractiveLoginEvent)
    {
      $user = $event->getAuthenticationToken()->getUser();
    }

    $cart = $this->cart_service->getCart();
    $this->cart_service->fromUser($cart, $user);
  }

  public function onRequest(GetResponseEvent $event)
  {
    try
    {
      //Редиректим со страницы входа пользователя в профиль, если он авторизован
      if ($this->authorization_checker->isGranted(User::ROLE_DEFAULT) && $event->getRequest()->get('_route') == self::AUTH_ROUTE)
      {
        $event->setResponse(new RedirectResponse($this->router->generate('homepage'), 302));
        #$event->setResponse(new RedirectResponse($this->router->generate('fos_user_profile_show'), 302));
      }
    }
    catch (AuthenticationCredentialsNotFoundException $e)
    {

    }
  }
}