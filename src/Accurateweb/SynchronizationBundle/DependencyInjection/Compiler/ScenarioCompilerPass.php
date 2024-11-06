<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 25.09.2018
 * Time: 12:14
 */

namespace Accurateweb\SynchronizationBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ScenarioCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    $settings = $container->findTaggedServiceIds('aw.synchronization.scenario');
    $manager = $container->findDefinition('aw.synchronization.scenario_manager');
    
    foreach ($settings as $id => $tags)
    {
      $manager->addMethodCall('addScenario', array(new Reference($id)));
    }
  }
}