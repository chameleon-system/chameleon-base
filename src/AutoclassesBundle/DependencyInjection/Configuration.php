<?php

namespace ChameleonSystem\AutoclassesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chameleon_system_autoclasses');
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->arrayNode('legacy_table_export')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('targetDir')
                            ->end()
                            ->scalarNode('configDir')
                                ->info('Path to the doctrine config folder')
                            ->end()
                            ->scalarNode('metaConfigDir')
                                ->info('Path where meta data (such as the yaml export for the old Tdb chains) will be stored.')
                                ->isRequired()
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
