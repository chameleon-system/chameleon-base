<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  ChameleonSystem\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('chameleon_system_core');
        $root->isRequired();

        $root
            ->children()
                ->scalarNode('vendor_dir')->end()
                ->scalarNode('redirectstrategy')
                    ->defaultValue('registershutdown')
                ->end()
                ->scalarNode('query_modifier_order_by_class')->end()
                ->arrayNode('pdo')
                    ->children()
                        ->scalarNode('mysql_attr_init_command')->end()
                    ->end()
                ->end()
                ->append($this->getCronjobConfig())
                ->append($this->getMailTargetTransformationServiceConfig())
                ->append($this->getMailerConfig())
                ->append($this->getGoogleMapsApiConfig())
                ->append($this->getModuleExecutionConfig());

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getCronjobConfig()
    {
        $tree = new TreeBuilder();
        $subTree = $tree->root('cronjobs');
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->arrayNode('ip_whitelist')
                ->defaultValue(array())
                ->prototype('scalar')->end()
            ->end()
            ->integerNode('fail_on_error_level')
                ->defaultValue(-1)
                ->info('A PHP error during a cronjob will terminate this cronjob if the error level is covered by
                    this setting (standard PHP error levels given as a bit mask, e.g. E_ALL & !E_NOTICE). If set to -1,
                    notices and deprecation warnings will be ignored in the prod environment while in other environments
                    all errors lead to termination of the job. This setting is intended for existing older cronjob
                    implementations; new code should throw exceptions directly and not use PHP errors.')
            ->end()
        ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getMailTargetTransformationServiceConfig()
    {
        $tree = new TreeBuilder();
        $subTree = $tree->root('mail_target_transformation_service');
        $subTree->isRequired();
        $subTree->children()
            ->scalarNode('target_mail')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->scalarNode('white_list')
                ->defaultValue('@PORTAL-DOMAINS')
            ->end()
            ->booleanNode('enabled')
                ->defaultValue(false)
            ->end()
            ->scalarNode('subject_prefix')->end()
        ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getMailerConfig()
    {
        $tree = new TreeBuilder();
        $subTree = $tree->root('mailer');
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->scalarNode('host')
                ->info('The mailer host. To use a port other than 25, add the port to the host with a colon (Example: myhost:123). To use TLS, prefix the host with "tls://".')
            ->end()
            ->scalarNode('user')
            ->end()
            ->scalarNode('password')
            ->end()
            ->enumNode('peer_security')
                ->values(array('strict', 'permissive'))
                ->defaultValue('permissive')
            ->end()
        ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getGoogleMapsApiConfig()
    {
        $tree = new TreeBuilder();
        $subTree = $tree->root('google_maps');
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->scalarNode('api_key')
                ->defaultNull()
            ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getModuleExecutionConfig()
    {
        $tree = new TreeBuilder();
        $subTree = $tree->root('module_execution');
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->enumNode('strategy')
                ->values(['inline', 'subrequest'])
                ->defaultValue('inline')
                ->info('Set to "subrequest" to execute modules as Symfony subrequests; set to "inline" to abstain from subrequests and improve performance')
            ->end();

        return $subTree;
    }
}
