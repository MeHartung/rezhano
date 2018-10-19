<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\PaymentBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PaymentMethodFeeCalculatorCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    // always first check if the primary service is defined
    if (!$container->has('accuratecommerce.payment.method.fee.calculator.repository'))
    {
      return;
    }

    $definition = $container->findDefinition('accuratecommerce.payment.method.fee.calculator.repository');

    // find all service IDs with the accuratecommerce.payment.method.fee.calculator tag
    $taggedServices = $container->findTaggedServiceIds('accuratecommerce.payment.method.fee.calculator');

    foreach ($taggedServices as $id => $tags)
    {
      // add the transport service to the ChainTransport service
      $definition->addMethodCall('addCalculator', array(new Reference($id)));
    }
  }


}