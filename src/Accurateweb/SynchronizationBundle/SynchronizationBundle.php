<?php

namespace Accurateweb\SynchronizationBundle;

use Accurateweb\SynchronizationBundle\DependencyInjection\Compiler\ConfigurationCompilerPass;
use Accurateweb\SynchronizationBundle\DependencyInjection\Compiler\DatasourceCompilerPass;
use Accurateweb\SynchronizationBundle\DependencyInjection\Compiler\ScenarioCompilerPass;
use Accurateweb\SynchronizationBundle\DependencyInjection\Compiler\SubjectCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SynchronizationBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new ScenarioCompilerPass());
    #$container->addCompilerPass(new DatasourceCompilerPass());
    $container->addCompilerPass(new ConfigurationCompilerPass());
    #$container->addCompilerPass(new SubjectCompilerPass());
  }
}