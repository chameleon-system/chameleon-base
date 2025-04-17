<?php

namespace ChameleonSystem\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chameleon_system_security');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode('google_login')
                ->canBeEnabled()
                ->children()
                    ->arrayNode('domain_to_base_user_mapping')
                        ->isRequired()
                        ->defaultValue([])
                        ->useAttributeAsKey('domain')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('domain')->info("G Suite/Google App domain. Users (G Suite/Google Apps) must be from this domain.")->end()
                                ->scalarNode('clone_user_permissions_from')->isRequired()->info('New users will be based on this user.')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('two_factor')
                ->canBeEnabled()
                ->children()
                ->scalarNode('enabled')
                    ->isRequired()
                    ->defaultValue(false)
                ->end()
            ->end();

        return $treeBuilder;
    }

}