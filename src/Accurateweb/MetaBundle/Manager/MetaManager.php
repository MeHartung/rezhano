<?php

namespace Accurateweb\MetaBundle\Manager;

use Accurateweb\MetaBundle\Model\Meta;
use Accurateweb\MetaBundle\Resolver\MetaResolverInterface;

class MetaManager implements MetaManagerInterface
{
  /**
   * @var MetaResolverInterface[]
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
  public function getMeta ()
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

  public function addResolver(MetaResolverInterface $metaResolver, $priority=null)
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