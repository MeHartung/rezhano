<?php


namespace StoreBundle\EventListener\Product;


use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\SEO\ProductRedirectRule;
use StoreBundle\Event\ProductNotFoundEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RedirectToNewProductListener
 *
 * @package StoreBundle\EventListener\Product
 */
class RedirectToNewProductListener
{
  private $entityManager, $router;
  
  public function __construct(EntityManagerInterface $entityManager, RouterInterface $router)
  {
    $this->entityManager = $entityManager;
    $this->router = $router;
  }
  
  /**
   * @param ProductNotFoundEvent $event
   * @return RedirectResponse|void
   */
  public function onNotFoundHttpException(ProductNotFoundEvent $event)
  {
    $slug = $event->getSlug();
    
    /** @var ProductRedirectRule $redirectRule */
    $redirectRule = $this->entityManager->getRepository(ProductRedirectRule::class)->findOneBy([
      'slugFrom' => $slug
    ]);
    
    if ($redirectRule === null)
    {
      return;
    }
    
    $url = $this->router->generate('product', ['slug' => $redirectRule->getSlugTo()], RouterInterface::ABSOLUTE_URL);
    $event->setResponse(new RedirectResponse($url, 301));
    return new RedirectResponse($url, 301);
  }
  
}