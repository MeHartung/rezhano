<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ShippingBundle;

use Accurateweb\ShippingBundle\DependencyInjection\Compiler\ShippingServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebShippingBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new ShippingServicePass());
  }
}