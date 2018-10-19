<?php
/**
 * Created by PhpStorm.
 * User: bukin
 * Date: 05.09.18
 * Time: 19:22
 */

namespace Accurateweb\TaxonomyBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TaxonPresentationResolverCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    $taxonomyResolvers = $container->findTaggedServiceIds('aw.taxon_presentaion.resolver');
    $manager = $container->findDefinition('aw.taxon_presentation.manager');

    foreach ($taxonomyResolvers as $id => $tags)
    {
      $manager->addMethodCall('addResolver', array(new Reference($id)));
    }
  }
}