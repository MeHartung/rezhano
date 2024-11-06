<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
namespace StoreBundle;

use StoreBundle\DependencyInjection\Compiler\ShippingServicePass;
use StoreBundle\DependencyInjection\CompilerPass\ProductPublicationManagerCompilerPass;
use StoreBundle\DependencyInjection\CompilerPass\StorePriceManagerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Description of StoreBundle
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class StoreBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    $container->addCompilerPass(new StorePriceManagerCompilerPass());
    $container->addCompilerPass(new ProductPublicationManagerCompilerPass());
  }
}
