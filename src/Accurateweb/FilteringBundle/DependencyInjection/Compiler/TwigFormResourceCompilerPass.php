<?php

namespace Accurateweb\FilteringBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class TwigFormResourceCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    if (!$container->hasParameter('twig.form.resources'))
    {
      return;
    }

    $container->setParameter('twig.form.resources', array_merge(
      array('AccuratewebFilteringBundle:Form:div_layout.html.twig'),
      $container->getParameter('twig.form.resources')
    ));
  }
}