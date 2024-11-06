<?php

namespace Accurateweb\MetaBundle;

use Accurateweb\MetaBundle\DependencyInjection\Compiler\MetaResolverCompilerPass;
use Accurateweb\MetaBundle\DependencyInjection\Compiler\OpenGraphMetaResolverCompilerPass;
use Accurateweb\MetaBundle\DependencyInjection\Compiler\OpenGraphRouteMetaCompilerPass;
use Accurateweb\MetaBundle\DependencyInjection\Compiler\RouteMetaCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebMetaBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new MetaResolverCompilerPass());
    $container->addCompilerPass(new RouteMetaCompilerPass());
    $container->addCompilerPass(new OpenGraphMetaResolverCompilerPass());
    $container->addCompilerPass(new OpenGraphRouteMetaCompilerPass());
  }
}
