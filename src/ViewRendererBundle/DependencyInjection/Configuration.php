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
        $treeBuilder = new TreeBuilder('chameleon_system_view_renderer');
        $root = $treeBuilder->getRootNode();
        $root->addDefaultsIfNotSet();

        $root
            ->children()
                ->scalarNode('css_dir')
                    ->defaultValue('chameleon/outbox/static/less')
                    ->info('Path used as working and output directory for the less compiler, relative to the web root directory. CAUTION: All files in this directory are deleted on cache clear.')
                ->end()
                ->scalarNode('static_content_url')
                    ->defaultValue('')
                    ->info('URL for static content (images, fonts, ...) if served from a different location (e.g. a CDN). Can be omitted. Default is empty.')
            ->end();

        return $treeBuilder;
    }
}
