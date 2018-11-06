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
                    ->defaultValue('chameleon/outbox/static/less')
                    ->info('Path used as working and output directory for the less compiler, relative to the web root directory. CAUTION: All files in this directory are deleted on cache clear.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
