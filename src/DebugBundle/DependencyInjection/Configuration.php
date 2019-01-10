<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DebugBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('chameleon_system_debug');

        $root
            ->children()
                ->append($this->getDatabaseProfilerConfig())
                ->booleanNode('backtrace_enabled')
                    ->setDeprecated('The "%path%.%node%" configuration key has been deprecated in Chameleon 6.3. Use the chameleon_system_debug: database_profiler: backtrace_enabled configuration key instead.')
                ->end()
                ->integerNode('backtrace_limit')
                    ->setDeprecated('The "%path%.%node%" configuration key has been deprecated in Chameleon 6.3. Use the chameleon_system_debug: database_profiler: backtrace_limit configuration key instead.')
                ->end()
            ->end();

        return $treeBuilder;
    }

    private function getDatabaseProfilerConfig(): NodeDefinition
    {
        $tree = new TreeBuilder();
        $subTree = $tree->root('database_profiler');
        $subTree->addDefaultsIfNotSet();
        $subTree->canBeEnabled();
        $subTree->children()
            ->booleanNode('backtrace_enabled')
                ->info('When enabled, adds a stacktrace for all queries in the profiler panel, so that queries can more easily be located.')
                ->defaultFalse()
            ->end()
            ->integerNode('backtrace_limit')
                ->info('When backtraces are enabled, this value controls the stack depth to be displayed. Higher values lead to higher memory consumption.')
                ->defaultValue(8)
            ->end()
        ;

        return $subTree;
    }
}
