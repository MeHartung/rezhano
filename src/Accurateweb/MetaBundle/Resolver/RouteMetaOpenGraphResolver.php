<?php

namespace Accurateweb\MetaBundle\Resolver;

use Accurateweb\MetaBundle\Model\MetaOpenGraphInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RouteMetaOpenGraphResolver implements MetaOpenGraphResolverInterface
{
  private $requestStack;
  private $metas;

  public function __construct (RequestStack $requestStack)
  {
    $this->requestStack = $requestStack;
    $this->metas = [];
  }

  public function addMeta($route, MetaOpenGraphInterface $meta)
  {
    $this->metas[$route] = $meta;
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getMeta ()
  {
    return $this->metas[$this->getRoute()];
  }

  /**
   * @inheritdoc
   */
  public function supports ()
  {
    if (!$this->getRoute())
    {
      return false;
    }

    return isset($this->metas[$this->getRoute()]);
  }

  private function getRoute()
  {
    if ($this->requestStack->getMasterRequest())
    {
      return $this->requestStack->getMasterRequest()->get('_route');
    }

    return null;
  }
}