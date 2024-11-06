<?php

namespace Accurateweb\SettingBundle;

use Accurateweb\SettingBundle\DependencyInjection\Compiler\SettingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 27.03.18
 * Time: 13:34
 */
class AccuratewebSettingBundle extends Bundle
{

  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new SettingCompilerPass());
  }
}