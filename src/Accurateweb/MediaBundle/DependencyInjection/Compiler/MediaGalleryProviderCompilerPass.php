<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MediaGalleryProviderCompilerPass implements CompilerPassInterface
{
  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   */
  public function process(ContainerBuilder $container)
  {
    if (!$container->has('aw.media.manager'))
    {
      return;
    }

    $definition = $container->findDefinition('aw.media.manager');

    $taggedServices = $container->findTaggedServiceIds('aw.media.gallery_provider');

    foreach ($taggedServices as $id => $tags)
    {
      foreach ($tags as $attributes)
      {
        $definition->addMethodCall('addGalleryProvider', array(
          new Reference($id),
          $attributes['alias']
        ));
      }
    }
  }

}