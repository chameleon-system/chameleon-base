<?php

namespace ChameleonSystem\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('chameleon_system_security');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode('googleLogin')
                ->canBeEnabled()
                ->children()
                    ->arrayNode('domainToBaseUserMapping')
                        ->useAttributeAsKey('domain')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('domain')->info("G Suite/Google App domain. Users (G Suite/Google Apps) must be from this domain.")->end()
                                ->scalarNode('cloneUserPermissionsFrom')->isRequired()->info('New users will be based on this user.')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

}