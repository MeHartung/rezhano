<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 04.10.2017
 * Time: 17:50
 */

namespace Accurateweb\SlugifierBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AccuratewebSlugifierExtension extends Extension
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
  }
}