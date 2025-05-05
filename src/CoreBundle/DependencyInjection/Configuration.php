<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chameleon_system_core');
        $root = $treeBuilder->getRootNode();
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
                ->append($this->getBackendConfig())
                ->append($this->getModuleExecutionConfig())
                ->append($this->getResourceCollectionConfig())
                ->append($this->getGeoJsonGeocoderConfig())
        ;

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getCronjobConfig()
    {
        $tree = new TreeBuilder('cronjobs');
        $subTree = $tree->getRootNode();
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->arrayNode('ip_whitelist')
                ->defaultValue([])
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
        $tree = new TreeBuilder('mail_target_transformation_service');
        $subTree = $tree->getRootNode();
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
        $tree = new TreeBuilder('mailer');
        $subTree = $tree->getRootNode();
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
                ->values(['strict', 'permissive'])
                ->defaultValue('strict')
            ->end()
        ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getGoogleMapsApiConfig()
    {
        $tree = new TreeBuilder('google_maps');
        $subTree = $tree->getRootNode();
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
    private function getGeoJsonGeocoderConfig()
    {
        $tree = new TreeBuilder('geocoder');
        $subTree = $tree->getRootNode();
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->scalarNode('geo_json_endpoint')
                ->defaultValue('https://nominatim.openstreetmap.org/search?format=geojson&q={query}')
                ->info('URL of a GeoJson geocoding endpoint. The {query} placeholder will be replaced by the string to search. Must respond to GET requests and must return a GeoJson FeatureCollection. Fetches data from nominatim by default but can be configurred to use a self hosted geocoder such as photon')
            ->end()
            ->arrayNode('attribution')
                ->info('Attribution information for the geocoding data. Set `show: false` if no attribution needs to be displayed')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('show')
                        ->defaultTrue()
                    ->end()
                    ->scalarNode('name')
                        ->defaultValue('nominatim')
                    ->end()
                    ->scalarNode('url')
                        ->defaultValue('https://nominatim.org/')
                    ->end()
                ->end()
            ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getModuleExecutionConfig()
    {
        $tree = new TreeBuilder('module_execution');
        $subTree = $tree->getRootNode();
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->enumNode('strategy')
                ->values(['inline', 'subrequest'])
                ->defaultValue('inline')
                ->info('Set to "subrequest" to execute modules as Symfony subrequests; set to "inline" to abstain from subrequests and improve performance.')
            ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getBackendConfig()
    {
        $tree = new TreeBuilder('backend');
        $subTree = $tree->getRootNode();
        $subTree->addDefaultsIfNotSet();
        $subTree->children()
            ->scalarNode('home_pagedef')
                ->defaultValue('welcome')
                ->info('The pagedef that is displayed after login and when clicking any "to home" button.')
            ->end()
            ->scalarNode('export_memory')
                ->defaultValue('1G')
                ->cannotBeEmpty()
                ->info('Configure php memory setting for backend exports as a byte unit (for example "180M"). Default value is 1G.')
            ->end();

        return $subTree;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getResourceCollectionConfig()
    {
        $tree = new TreeBuilder('resource_collection');
        $subTree = $tree->getRootNode();
        $subTree->addDefaultsIfNotSet();
        $outboxUrl = rtrim(URL_OUTBOX, '/');
        // the outboxurl may be an absolute url - we want a relative url instead. Reason:
        // 1. if the container is built using the console, then the URL_OUTBOX will have "console" as a domain
        // 2. The URL is added via \TGlobalBase::GetStaticURL which will take care of adding the correct url.
        $defaultCacheUrlPath = parse_url($outboxUrl, PHP_URL_PATH);
        $subTree->children()
            ->scalarNode('cache_path')
                ->defaultValue(rtrim(PATH_OUTBOX, '/').'/static')
                ->info('The path on the server where resource collection files will be stored.')
            ->end()
            ->scalarNode('cache_url_path')
                ->defaultValue($defaultCacheUrlPath.'/static')
                ->info('The URL path part to the files generated by the resource collection (relative to /).')
            ->end();

        return $subTree;
    }
}
