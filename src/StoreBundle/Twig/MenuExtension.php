<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Twig;

use StoreBundle\Entity\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuExtension extends \Twig_Extension
{
  private $request;

  function __construct(RequestStack $requestStack)
  {
    $this->request = $requestStack->getCurrentRequest();
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('menu_url', array($this, 'menuUrl')),
    );
  }

  public function menuUrl($url)
  {
    if (strlen($url) && preg_match('/^\/([^\/]|$)/', $url))
    {
      $url = $this->request->getBaseUrl() . $url;
    }

    return $url;
  }
}