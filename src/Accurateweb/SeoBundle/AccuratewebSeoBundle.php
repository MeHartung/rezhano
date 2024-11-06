<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 18:46
 */

namespace Accurateweb\SeoBundle;

use Accurateweb\SeoBundle\DependencyInjection\Compiler\SitemapBuilderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebSeoBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new SitemapBuilderCompilerPass());
  }
}