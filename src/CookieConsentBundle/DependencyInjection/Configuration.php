<?php

namespace ChameleonSystem\CookieConsentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('chameleon_system_cookie_consent');
        $root = $treeBuilder->getRootNode();
        $root->isRequired();
        $root->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('position')
                        ->defaultValue('bottom')
                    ->end()
                    ->scalarNode('theme')
                        ->defaultValue('classic')
                    ->end()
                    ->scalarNode('bg_color')
                        ->defaultValue('#363636')
                    ->end()
                    ->scalarNode('button_bg_color')
                        ->defaultValue('#46a546')
                    ->end()
                    ->scalarNode('button_text_color')
                        ->defaultValue('#ffffff')
                    ->end()
                    ->scalarNode('privacy_policy_system_page_name')
                        ->defaultValue('privacy-policy')
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
