<?php

namespace Accurateweb\MetaBundle\Manager;

use Accurateweb\MetaBundle\Resolver\MetaOpenGraphResolverInterface;

class MetaOpenGraphManager implements MetaOpenGraphManagerInterface
{
  /**
   * @var MetaOpenGraphResolverInterface[]
   */
  private $resolvers;
  private $currentIndex=0;

  public function __construct ()
  {
    $this->resolvers = [];
  }

  /**
   * @inheritdoc
   */
  public function getMetaOpenGraph ()
  {
    ksort($this->resolvers);

    foreach ($this->resolvers as $resolver)
    {
      if ($resolver->supports())
      {
        return $resolver->getMeta();
      }
    }

    return null;
  }

  public function addResolver(MetaOpenGraphResolverInterface $metaResolver, $priority=null)
  {
    if (is_int($priority) && !isset($this->resolvers[$priority]))
    {
      $this->resolvers[$priority] = $metaResolver;
    }
    else
    {
      $this->resolvers[$this->currentIndex++] = $metaResolver;
    }

    return $this;
  }

}