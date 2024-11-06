<?php

namespace StoreBundle\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class StorePriceManagerCompilerPass implements CompilerPassInterface
{
  public function process (ContainerBuilder $container)
  {
    $priceManager = $container->getDefinition('store.price.manager');
    $modificators = $container->findTaggedServiceIds('store.price.modificator');

    foreach ($modificators as $id => $tags)
    {
      $priceManager->addMethodCall('addProductPriceModificator', [new Reference($id)]);
    }
  }
}