<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 17.06.2017
 * Time: 20:00
 */

namespace Accurateweb\SphinxSearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AccuratewebSphinxSearchExtension extends Extension
{
  public function load(array $configs, ContainerBuilder $container)
  {
    $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
    $loader->load('services.xml');

    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $def = $container->getDefinition('accurateweb.sphinxsearch');
    $def->replaceArgument(1, $config['searchd']);
  }
}