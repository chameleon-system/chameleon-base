<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsChangeLogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('chameleon_system_cms_changelog');
        $root = $treeBuilder->getRootNode();
        $root->isRequired();
        $root->addDefaultsIfNotSet()
                ->children()
                    ->integerNode('days')
                    ->info('Number of days to keep change log table entries. 0 disables the deletion. Default is 180.')
                    ->defaultValue(180)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
