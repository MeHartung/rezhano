<?php

namespace StoreBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProductPublicationManagerCompilerPass implements CompilerPassInterface
{
  public function process (ContainerBuilder $container)
  {
    $manager = $container->getDefinition('store.product.publication.manager');
    $resolvers = $container->findTaggedServiceIds('product.publication.resolver');

    foreach ($resolvers as $id => $tags)
    {
      $manager->addMethodCall('addPublicationResolver', [new Reference($id)]);
    }
  }
}