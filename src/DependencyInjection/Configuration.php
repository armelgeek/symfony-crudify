<?php

namespace ArmelWanes\Crudify\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('maker');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('maker');
        }

        $rootNode
            ->children()
                ->scalarNode('root_namespace')->defaultValue('App')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

