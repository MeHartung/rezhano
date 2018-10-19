<?php

namespace Accurateweb\MetaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MetaResolverCompilerPass implements CompilerPassInterface
{
  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   */
  public function process (ContainerBuilder $container)
  {
    if (!$container->has('aw.meta.manager'))
    {
      return;
    }

    $definition = $container->findDefinition('aw.meta.manager');

    $taggedServices = $container->findTaggedServiceIds('aw.meta.resolver');

    foreach ($taggedServices as $id => $tags)
    {
      $priority = null;

      foreach ($tags as $attributes)
      {
        $priority = isset($attributes['priority'])?$attributes['priority']:null;
      }

      $definition->addMethodCall('addResolver', array(
        new Reference($id),
        $priority
      ));
    }
  }

}