<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\CdekShippingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AccuratewebCdekShippingExtension extends Extension
{
  /**
   * Loads a specific configuration.
   *
   * @param array $configs An array of configuration values
   * @param ContainerBuilder $container A ContainerBuilder instance
   *
   * @throws \InvalidArgumentException When provided tag is not defined in this extension
   */
  public function load(array $configs, ContainerBuilder $container)
  {
    $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
    $loader->load('services.xml');

    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $def = $container->getDefinition('accuratecommerce.shipping.service.cdek');
    $def->replaceArgument(2, $config['tariffs']['courier']);
    $def->replaceArgument(3, $config['tariffs']['pickup']);
  }

}