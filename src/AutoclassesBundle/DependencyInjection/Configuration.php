<?php

namespace ChameleonSystem\AutoclassesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('chameleon_system_data_access');
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->arrayNode('legacy_table_export')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('targetDir')
                            ->end()
                            ->scalarNode('configDir')
                            ->end()
                            ->scalarNode('namespace')
                            ->end()
                            ->arrayNode('tables')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

}