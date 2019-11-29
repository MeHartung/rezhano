<?php


namespace StoreBundle\EventListener\Request;


use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class YamapTokenInject implements EventSubscriberInterface
{
  private $twig;
  private $settingManager;

  public function __construct (\Twig_Environment $twig, SettingManagerInterface $settingManager)
  {
    $this->twig = $twig;
    $this->settingManager = $settingManager;
  }

  public function onRequest (FilterControllerEvent $event)
  {
    if ($event->isMasterRequest())
    {
      $this->twig->addGlobal('yamap_token', $this->settingManager->getValue('yamap_token'));
    }
  }

  public static function getSubscribedEvents ()
  {
    return [
      KernelEvents::CONTROLLER => 'onRequest',
    ];
  }

}