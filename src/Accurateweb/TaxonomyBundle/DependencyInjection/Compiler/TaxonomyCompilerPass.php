<?php

namespace Accurateweb\TaxonomyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TaxonomyCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    $taxonomyResolvers = $container->findTaggedServiceIds('aw.taxonomy.resolver');
    $manager = $container->findDefinition('aw.taxonomy.manager');

    foreach ($taxonomyResolvers as $id => $tags)
    {
      $manager->addMethodCall('addTaxonomyResolver', array(new Reference($id)));
    }
  }
}