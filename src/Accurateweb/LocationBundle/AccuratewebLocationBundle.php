<?php

namespace Accurateweb\LocationBundle;

use Accurateweb\LocationBundle\DependencyInjection\CompilerPass\LocationResolverCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebLocationBundle extends Bundle
{
  public function build (ContainerBuilder $container)
  {
    $container->addCompilerPass(new LocationResolverCompilerPass());
  }
}
