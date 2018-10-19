<?php

namespace Accurateweb\TaxonomyBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TaxonPresentationRendererCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    $taxonomyResolvers = $container->findTaggedServiceIds('aw.taxon_renderer');
    $manager = $container->findDefinition('aw.taxon_renderer.manager');

    foreach ($taxonomyResolvers as $id => $tags)
    {
      $manager->addMethodCall('addRenderer', array(new Reference($id)));
    }
  }
}