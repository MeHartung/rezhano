<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 07.05.2018
 * Time: 18:36
 */

namespace Accurateweb\TaxonomyBundle;

use Accurateweb\TaxonomyBundle\DependencyInjection\Compiler\TaxonomyCompilerPass;
use Accurateweb\TaxonomyBundle\DependencyInjection\Compiler\TaxonPresentationRendererCompilerPass;
use Accurateweb\TaxonomyBundle\DependencyInjection\Compiler\TaxonPresentationResolverCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TaxonomyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TaxonomyCompilerPass());
        $container->addCompilerPass(new TaxonPresentationResolverCompilerPass());
        $container->addCompilerPass(new TaxonPresentationRendererCompilerPass());
    }
}