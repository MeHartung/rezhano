<?php

namespace Accurateweb\SynchronizationBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigurationCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    $settings = $container->findTaggedServiceIds('aw.synchronization.config');
    $manager = $container->findDefinition('aw.synchronization.configuration_manager');
    
    foreach ($settings as $id => $tags)
    {
      $manager->addMethodCall('addConfig', array(new Reference($id)));
    }
  }
}

{
  
}