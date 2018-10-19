<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 29.11.17
 * Time: 18:43
 */

namespace StoreBundle\EventListener;


use StoreBundle\Controller\CreditController;
use StoreBundle\Controller\Order\CheckoutController;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Route;

class CheckoutSubscriber implements EventSubscriberInterface
{

  private $route, $container;

  public function __construct(Router $route, Container $container)
  {
    $this->route = $route;
    $this->container = $container;
  }

  public function onKernelController(FilterControllerEvent $event)
  {
    $controller = $event->getController();

    /*
     * $controller passed can be either a class or a Closure.
     * This is not usual in Symfony but it may happen.
     * If it is a class, it comes in array format
     */

    if (!is_array($controller))
    {
      return;
    }
    if ($controller[0] instanceof CheckoutController)
    {
      if ($event->getController()[1] == 'completeAction')
      {
        $documentNumber = $event->getRequest()->get('documentNumber');

        $isThisCredit = $this->container->get('store.user.credit')->isThisCredit(null, $documentNumber);

        if ($isThisCredit !== false)
        {
          $creditName = $this->container->get('store.user.credit')->getBankName(null, $documentNumber);

          if ($creditName == 'tinkoff')
          {
            $redirectUrl = $this->route->generate('credit_tinkoff', ['documentNumber' => $documentNumber]);

            $event->setController(function () use ($redirectUrl)
            {
              return new RedirectResponse($redirectUrl);
            });

          } elseif ($creditName == 'alfa-bank')
          {
            $redirectUrl = $this->route->generate('credit_alfabank', ['documentNumber' => $documentNumber]);

            $event->setController(function () use ($redirectUrl)
            {
              return new RedirectResponse($redirectUrl);
            });
          }
        }
      }
    }
  }

  public static function getSubscribedEvents()
  {
    return array(
      KernelEvents::CONTROLLER => 'onKernelController',
      KernelEvents::RESPONSE => 'onKernelController',
    );
  }

}