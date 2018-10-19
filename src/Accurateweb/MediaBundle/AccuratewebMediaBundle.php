<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle;

use Accurateweb\MediaBundle\DependencyInjection\Compiler\MediaGalleryProviderCompilerPass;
use Accurateweb\MediaBundle\DependencyInjection\Compiler\TwigFormResourceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebMediaBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new MediaGalleryProviderCompilerPass());
    $container->addCompilerPass(new TwigFormResourceCompilerPass());
  }
}