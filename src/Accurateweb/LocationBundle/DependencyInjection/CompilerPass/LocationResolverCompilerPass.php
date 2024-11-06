<?php

namespace Accurateweb\LocationBundle\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LocationResolverCompilerPass implements CompilerPassInterface
{
  public function process (ContainerBuilder $container)
  {
    $location = $container->getDefinition('aw.location');
    $resolvers = $container->findTaggedServiceIds('aw.location.resolver');

    foreach ($resolvers as $id => $tags)
    {
      $priority = null;

      foreach ($tags as $tag)
      {
        $priority = isset($tag['priority'])?$tag['priority']:null;
      }

      $location->addMethodCall('addLocationResolver', [new Reference($id), $priority]);
    }
  }
}