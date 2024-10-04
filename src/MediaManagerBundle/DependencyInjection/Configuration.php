<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\DependencyInjection;

use RuntimeException;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function getConfigTreeBuilder(): \Symfony\Component\Config\Definition\Builder\TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chameleon_system_media_manager');
        $root = $treeBuilder->getRootNode();
        // @formatter:off

        $root
            ->children()
                ->booleanNode('open_in_new_window')
                    ->defaultFalse()
                ->end()
                ->arrayNode('available_page_sizes')
                    ->requiresAtLeastOneElement()
                    ->prototype('integer')
                    ->end()
                    ->defaultValue(array(12, 24, 48, 96, 204, 504, -1))
                ->end()
                ->integerNode('default_page_size')
                    ->defaultValue(204)
                ->end()
            ->end();
        // @formatter:on

        return $treeBuilder;
    }
}
