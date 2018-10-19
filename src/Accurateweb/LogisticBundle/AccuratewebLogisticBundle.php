<?php

namespace Accurateweb\LogisticBundle;

use Accurateweb\LogisticBundle\DependencyInjection\CompilerPass\AvailabilityManagerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebLogisticBundle extends Bundle
{
  public function build (ContainerBuilder $container)
  {
    $container->addCompilerPass(new AvailabilityManagerCompilerPass());
  }

}
