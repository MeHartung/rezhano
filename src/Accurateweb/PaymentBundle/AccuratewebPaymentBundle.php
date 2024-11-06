<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\PaymentBundle;

use Accurateweb\PaymentBundle\DependencyInjection\Compiler\PaymentMethodAvailabilityDecisionManagerCompilerPass;
use Accurateweb\PaymentBundle\DependencyInjection\Compiler\PaymentMethodFeeCalculatorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebPaymentBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new PaymentMethodAvailabilityDecisionManagerCompilerPass());
    $container->addCompilerPass(new PaymentMethodFeeCalculatorCompilerPass());
  }
}