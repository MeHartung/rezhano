<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 18:47
 */

namespace Accurateweb\SeoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SitemapBuilderCompilerPass implements CompilerPassInterface
{
  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   */
  public function process(ContainerBuilder $container)
  {
    if (!$container->has('aw_seo.sitemap.builder'))
    {
      return;
    }

    $definition = $container->findDefinition('aw_seo.sitemap.builder');

    $loaders = $container->findTaggedServiceIds('aw_seo.sitemap.loader');

    foreach ($loaders as $id => $tags)
    {
      foreach ($tags as $attributes)
      {
        $definition->addMethodCall('addLoader', array(
          new Reference($id),
          isset($attributes['priority']) ? $attributes['priority'] : null
        ));
      }
    }

    $extractors = $container->findTaggedServiceIds('aw_seo.sitemap.extractor');

    foreach ($extractors as $id => $tags)
    {
      foreach ($tags as $attributes)
      {
        $definition->addMethodCall('addExtractor', array(
          new Reference($id),
          isset($attributes['priority']) ? $attributes['priority'] : null
        ));
      }
    }


  }

}