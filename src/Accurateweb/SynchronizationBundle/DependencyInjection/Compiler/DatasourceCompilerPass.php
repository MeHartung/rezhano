<?php

namespace Accurateweb\SynchronizationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DatasourceCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    $settings = $container->findTaggedServiceIds('aw.synchronization.datasource');
    $manager = $container->findDefinition('aw.synchronization.datasource_manager');
    
    foreach ($settings as $id => $tags)
    {
      $manager->addMethodCall('addScenario', array(new Reference($id)));
    }
  }
}
