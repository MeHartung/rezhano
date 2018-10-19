<?php

namespace Accurateweb\SettingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SettingCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    $settings = $container->findTaggedServiceIds('aw.setting');
    $manager = $container->findDefinition('aw.settings.manager');

    foreach ($settings as $id => $tags)
    {
      $manager->addMethodCall('addSetting', array(new Reference($id)));
    }
  }
}