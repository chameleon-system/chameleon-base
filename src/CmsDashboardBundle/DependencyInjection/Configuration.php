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
                ->scalarNode('google_search_console_domain_property')
                ->defaultValue('')
                ->info('Google Search Console Domain Property')
            ->end()
            ->integerNode('google_search_console_period_days')
                ->defaultValue(28)
                ->info('the time period in days for the google search console widget')
            ->end()
            ->scalarNode('google_analytics_property_id')
                ->defaultValue('')
                ->info('Google Analytics Property Id')
            ->end()
                ->integerNode('google_analytics_period_days')
                ->defaultValue(28)
                ->info('the time period in days for the google analytics widget')
            ->end()
            ->scalarNode('google_api_auth_json')
                ->defaultValue('')
                ->info('Google API Auth JSON.')
            ->end()
        ->end();

        return $treeBuilder;
    }
}
