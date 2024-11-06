<?php

namespace Accurateweb\MetaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class RouteMetaCompilerPass implements CompilerPassInterface
{
  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   */
  public function process (ContainerBuilder $container)
  {
    if (!$container->has('aw.meta.route_resolver'))
    {
      return;
    }

    $definition = $container->findDefinition('aw.meta.route_resolver');

    $taggedServices = $container->findTaggedServiceIds('aw.meta.route');

    foreach ($taggedServices as $id => $tags)
    {
      foreach ($tags as $attributes)
      {
        if (isset($attributes['route']))
        {
          $definition->addMethodCall('addMeta', array(
            $attributes['route'],
            new Reference($id)
          ));
        }
      }
    }
  }

}