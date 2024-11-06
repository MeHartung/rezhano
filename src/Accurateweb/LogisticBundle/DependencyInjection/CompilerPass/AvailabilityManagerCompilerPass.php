<?php

namespace Accurateweb\LogisticBundle\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AvailabilityManagerCompilerPass implements CompilerPassInterface
{
  public function process (ContainerBuilder $container)
  {
    $manager = $container->getDefinition('aw.logistic.availability.manager');
    $validators = $container->findTaggedServiceIds('aw.logistic.availability.validator');

    foreach ($validators as $id => $tags)
    {
      $manager->addMethodCall('addAvailabilityValidator', [new Reference($id)]);
    }
  }
}