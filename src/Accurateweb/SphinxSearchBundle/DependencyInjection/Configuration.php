<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 15.06.2017
 * Time: 21:49
 */

namespace Accurateweb\SphinxSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('accurateweb_sphinx_search');

    $rootNode
      ->children()
        ->arrayNode('searchd')
          ->children()
            ->scalarNode('host')->defaultValue('localhost')->end()
            ->integerNode('port')->defaultValue(9312)->end()
            ->integerNode('limit')->end()
            ->scalarNode('binary_path')->end()
//      '' => 100000,
//      'mode' => sfSphinxClient::SPH_MATCH_EXTENDED,
//      'sort' => sfSphinxClient::SPH_SORT_EXTENDED,
//      'sortby' => '@weight DESC',
//      'ranker' => sfSphinxClient::SPH_RANK_SPH04, /* SPH_RANK_WORDCOUNT */
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }
}