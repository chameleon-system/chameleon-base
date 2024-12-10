<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsTextFieldBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chameleon_system_cms_text_field');
        $root = $treeBuilder->getRootNode();
        $root
            ->isRequired()
            ->addDefaultsIfNotSet();

        $root
            ->children()
                ->booleanNode('allow_script_tags')
                    ->defaultFalse()
                ->info('If set to false, script tags are removed on display. This is a security feature: 
                If an attacker somehow manages to gain access to the backend, they cannot inject scripts.
                Consider setting this option to false if script support in text fields is not required.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
