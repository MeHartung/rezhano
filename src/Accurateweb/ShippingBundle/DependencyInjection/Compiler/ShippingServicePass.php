<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ShippingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ShippingServicePass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    // always first check if the primary service is defined
    if (!$container->has('accurateweb.shipping.manager'))
    {
      return;
    }

    $definition = $container->findDefinition('accurateweb.shipping.manager');

    // find all service IDs with the app.mail_transport tag
    $taggedServices = $container->findTaggedServiceIds('accuratecommerce.shipping.service');

    foreach ($taggedServices as $id => $tags)
    {
      // add the transport service to the ChainTransport service
      $definition->addMethodCall('registerShippingService', array(new Reference($id)));
    }
  }
}