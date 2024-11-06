<?php

namespace StoreBundle\Event;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductNotFoundEvent extends Event
{
  /**
   * @var string - слаг не найденного товара
   */
  private $slug, $response;
  
  /**
   * ProductNotFoundEvent constructor.
   *
   * @param string $slug
   */
  public function __construct($slug)
  {
    $this->slug = $slug;
  }
  
  /** @return string */
  public function getSlug()
  {
    return $this->slug;
  }
  
  /**
   * @return Response|RedirectResponse
   */
  public function getResponse()
  {
    return $this->response;
  }
  
  /**
   * @param ResponseInterface $response
   */
  public function setResponse($response): void
  {
    if (!$response instanceof Response)
    {
      throw new \RuntimeException('$response must instanceof Symfony\Component\HttpFoundation\Response!');
    }
    
    $this->response = $response;
  }
}