<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigFormResourceCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    if (!$container->hasParameter('twig.form.resources'))
    {
      return;
    }

    $container->setParameter('twig.form.resources', array_merge(
      array('AccuratewebMediaBundle:Form:div_layout.html.twig'),
      $container->getParameter('twig.form.resources')
    ));
  }
}