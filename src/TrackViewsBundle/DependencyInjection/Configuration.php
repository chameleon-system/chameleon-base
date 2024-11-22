<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TrackViewsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chameleon_system_track_views');
        $root = $treeBuilder->getRootNode();

        $root
            ->canBeDisabled()
            ->children()
                ->scalarNode('target_table')
                    ->defaultValue('pkg_track_object')
                ->end()
                ->integerNode('time_to_live')
                    ->defaultValue(3600)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
