<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 13.12.2017
 * Time: 18:57
 */

namespace Accurateweb\CdekShippingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  /**
   * Generates the configuration tree builder.
   *
   * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
   */
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('accurateweb_cdek_shipping');

    $rootNode
      ->children()
      ->arrayNode('tariffs')
      ->children()
      ->integerNode('pickup')->end()
      ->integerNode('courier')->end()
      ->end()
      ->end() // twitter
      ->end()
    ;

    return $treeBuilder;
  }

}