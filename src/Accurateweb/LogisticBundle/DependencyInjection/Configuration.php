<?php

namespace Accurateweb\LogisticBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
  /**
   * {@inheritdoc}
   */
  public function getConfigTreeBuilder ()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('accurateweb_logistic');

    $rootNode
      ->children()
        ->arrayNode('entities')
          ->children()
            ->scalarNode('warehouse')->isRequired()->end()
            ->scalarNode('city')->isRequired()->end()
            ->scalarNode('pickup_point')->isRequired()->end()
            ->scalarNode('product_stock')->isRequired()->end()
            ->scalarNode('product_stockable')->isRequired()->end()
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }
}
