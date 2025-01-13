<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('chameleon_system_cms_dashboard');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('cache_ttl')
                    ->defaultValue(86400)
                    ->min(0)
                    ->info('Cache Time-To-Live in seconds for dashboard widgets.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
