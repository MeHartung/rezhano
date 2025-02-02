<?php

namespace Accurateweb\ClientApplicationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ClientApplicationModelAdapterCompilerPass implements CompilerPassInterface
{
  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   */
  public function process (ContainerBuilder $container)
  {
    if (!$container->has('aw.client_application.manager'))
    {
      return;
    }

    $definition = $container->findDefinition('aw.client_application.manager');
    $taggedServices = $container->findTaggedServiceIds('aw.client_application.adapter');

    foreach ($taggedServices as $id => $tags)
    {
      foreach ($tags as $attributes)
      {
        $alias = isset($attributes['alias'])?$attributes['alias']:$id;

        $definition->addMethodCall('addModelAdapter', array(
          new Reference($id),
          $alias
        ));
      }
    }
  }

}