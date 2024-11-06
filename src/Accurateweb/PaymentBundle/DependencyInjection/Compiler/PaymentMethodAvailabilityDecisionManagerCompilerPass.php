<?php

namespace Accurateweb\PaymentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class PaymentMethodAvailabilityDecisionManagerCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    // always first check if the primary service is defined
    if (!$container->has('accuratecommerce.payment.method.manager'))
    {
      return;
    }

    $definition = $container->findDefinition('accuratecommerce.payment.method.manager');

    // find all service IDs with the app.mail_transport tag
    $taggedServices = $container->findTaggedServiceIds('accuratecommerce.payment.method.availability.decision.manager');

    foreach ($taggedServices as $id => $tags)
    {
      // add the transport service to the ChainTransport service
      $definition->addMethodCall('addAvailabilityDecisionManager', array(new Reference($id)));
    }
  }
}