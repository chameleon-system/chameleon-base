<?php

namespace ChameleonSystem\ViewRendererBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('chameleon_system_view_renderer');
        $root->addDefaultsIfNotSet();

        $root
            ->children()
                ->scalarNode('css_dir')
                    ->defaultValue('/chameleon/outbox/static/less')
                    ->info('Directory path to where the less compiler cache works and the output files are stored. Relative to the web root directory.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
