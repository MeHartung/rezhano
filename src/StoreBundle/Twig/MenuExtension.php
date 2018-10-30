<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Twig;

use StoreBundle\Entity\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;

class MenuExtension extends \Twig_Extension
{
  private $request, $router;
  
  function __construct(RequestStack $requestStack, Router $router)
  {
    $this->request = $requestStack->getCurrentRequest();
    $this->router = $router;
  }
  
  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('menu_url', array($this, 'menuUrl')),
      new \Twig_SimpleFilter('is_active', array($this, 'isActive')),
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
  
  /**
   * Активен ли пункт меню
   *
   * @param $menuUrl
   * @return bool
   */
  public function isActive($menuUrl)
  {
    $menuRoute = $this->router->match($menuUrl)['_route'];
    $currentRoute = $this->request->get('_route');
    
    $currentRoutePrefix = substr($menuRoute, 0, strpos($menuRoute, '_'));
    
    if (is_bool($currentRoutePrefix)) return false;
    
    if (strlen($currentRoutePrefix) > 0)
    {
      $menuRoute == $currentRoute || strpos($currentRoute, $currentRoutePrefix) !== false ||
      ($menuRoute == 'catalog_index' && $currentRoute == 'taxon');
    }
    else
    {
      return $menuRoute == $currentRoute;
    }
    
    return false;
  }
}