<?php

namespace Accurateweb\ClientApplicationBundle;

use Accurateweb\ClientApplicationBundle\DependencyInjection\Compiler\ClientApplicationModelAdapterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebClientApplicationBundle extends Bundle
{
  public function build (ContainerBuilder $container)
  {
    $container->addCompilerPass(new ClientApplicationModelAdapterCompilerPass());
  }
}