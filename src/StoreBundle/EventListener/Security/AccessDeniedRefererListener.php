<?php

namespace StoreBundle\EventListener\Security;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class AccessDeniedRefererListener
{
  public function onResponse(FilterResponseEvent $event)
  {
    $response = $event->getResponse();
    $request = $event->getRequest();

    /*
     * Если нас редиректит на логин, но не с логина, но запоминаем страницу, на которую хотели перейти
     */
    if ($response instanceof RedirectResponse && !$request->isXmlHttpRequest()
      && preg_match('/\/login$/', $response->getTargetUrl())
      && preg_match('/\/login$/', $request->getPathInfo())
    )
    {
      $request->getSession()->set('_security.main.target_path', $request->getPathInfo());
    }
  }
}