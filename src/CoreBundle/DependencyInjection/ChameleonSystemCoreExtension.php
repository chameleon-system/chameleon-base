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

use ChameleonSystem\CoreBundle\CoreEvents;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChameleonSystemCoreExtension extends Extension
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);

        // get standard configs
        $aConfigDirs = array(
            PATH_CORE_CONFIG,
            _CMS_CUSTOM_CORE.'/config/',
            _CMS_CUSTOMER_CORE.'/../config/',
        );
        foreach ($aConfigDirs as $sConfigDir) {
            $this->loadConfigFile($container, $sConfigDir, 'services.xml');
            $this->loadConfigFile($container, $sConfigDir, 'mail.xml');
            $this->loadConfigFile($container, $sConfigDir, 'data_access.xml');
            $this->loadConfigFile($container, $sConfigDir, 'checks.xml');
            $this->loadConfigFile($container, $sConfigDir, 'urlnormalization.xml');
            $this->loadConfigFile($container, $sConfigDir, 'universal_uploader.xml');
            $this->loadConfigFile($container, $sConfigDir, 'database_migration.xml');
            $this->loadConfigFile($container, $sConfigDir, 'cronjobs.xml');
            $this->loadConfigFile($container, $sConfigDir, 'mappers.xml');
            $this->loadConfigFile($container, $sConfigDir, 'factory.xml');
        }

        $this->addMailTransformationConfig($config['mail_target_transformation_service'], $container);
        $this->addRedirectConfig($config, $container);
        $this->addCronjobConfig($config['cronjobs'], $container);
        $this->addCacheConfig($container);
        $this->addMailerConfig($config['mailer'], $container);
        $this->addGoogleApiConfig($config['google_maps'], $container);
        $this->addModuleExecutionConfig($config['module_execution'], $container);
        $this->configureSession($container);
        $this->addBackendConfig($config['backend'], $container);

        $this->addResources($container);

        $this->configureResourceCollectorService($config['resource_collection'], $container);
    }

    private function configureResourceCollectorService(
        array $resourceCollectionConfiguration,
        ContainerBuilder $container
    ): void {
        $resourceCollectionDefinition = $container->getDefinition('chameleon_system_core.resource_collector');
        $resourceCollectionDefinition->replaceArgument(4, $resourceCollectionConfiguration['cache_url_path']);
        $resourceCollectionDefinition->replaceArgument(5, $resourceCollectionConfiguration['cache_path']);
    }

    private function loadConfigFile(ContainerBuilder $container, string $sConfigDir, string $filename): void
    {
        $loader = new XmlFileLoader($container, new FileLocator($sConfigDir));
        try {
            $loader->load($filename);
        } catch (\InvalidArgumentException $e) {
            // services.xml not found
        }
    }

    private function addMailTransformationConfig(array $config, ContainerBuilder $container): void
    {
        foreach ($config as $key => $value) {
            $container->setParameter("chameleon_system_core.mail_target_transformation_service.$key", $value);
        }

        if (true === $config['enabled']) {
            $container->setAlias('chameleon_system_core.mail_target_transformation_service', 'chameleon_system_core.transforming_mail_target_transformation_service');
        } else {
            $container->setAlias('chameleon_system_core.mail_target_transformation_service', 'chameleon_system_core.null_mail_target_transformation_service');
        }
    }

    private function addRedirectConfig(array $config, ContainerBuilder $container): void
    {
        if ('throwexception' === $config['redirectstrategy']) {
            $definition = $container->getDefinition('chameleon_system_core.redirect');
            $args = $definition->getArguments();
            $ref = new Reference('chameleon_system_core.redirectstrategy.throwexception');
            $args[1] = $ref;
            $definition->setArguments($args);
        }
    }

    private function addCronjobConfig(array $cronjobConfig, ContainerBuilder $container): void
    {
        $backendAccessCheckDefinition = $container->getDefinition('chameleon_system_core.security.backend_access_check');
        $backendAccessCheckDefinition->addMethodCall('unrestrictPagedef', array('runcrons', $cronjobConfig['ip_whitelist']));

        $failOnErrorLevel = $cronjobConfig['fail_on_error_level'];
        if (-1 === $failOnErrorLevel) {
            $debug = $container->getParameter('kernel.debug');
            if (true === $debug) {
                $failOnErrorLevel = E_ALL & !E_DEPRECATED & !E_USER_DEPRECATED;
            } else {
                $failOnErrorLevel = E_ALL & !E_NOTICE & !E_USER_NOTICE & !E_DEPRECATED & !E_USER_DEPRECATED;
            }
        }
        $container->setParameter('chameleon_system_core.cronjobs.fail_on_error_level', $failOnErrorLevel);
    }

    private function addCacheConfig(ContainerBuilder $container): void
    {
        if ($container->getParameter('chameleon_system_core.cache.allow')
            && $container->getParameter('chameleon_system_core.cache.memcache_activate')) {
            $this->addMemcacheConfig($container);
        }
        $this->addRequestCacheConfig($container);
    }

    private function addMemcacheConfig(ContainerBuilder $container): void
    {
        $cacheCmsPortalDomainsDataAccessDefinition = $container->getDefinition('chameleon_system_core.data_access_cms_portal_domains_cache_decorator');
        $cacheCmsPortalDomainsDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_portal_domains');

        $cacheRoutingUtilDefinition = $container->getDefinition('chameleon_system_core.util.routing_util_cache_decorator');
        $cacheRoutingUtilDefinition->setDecoratedService('chameleon_system_core.util.routing');

        $cacheCmsPortalSystemPageDataAccessDefinition = $container->getDefinition('chameleon_system_core.cache_data_access_cms_portal_system_page');
        $cacheCmsPortalSystemPageDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_portal_system_page');

        $cacheCmsTplPageDataAccessDefinition = $container->getDefinition('chameleon_system_core.cache_data_access_cms_tpl_page');
        $cacheCmsTplPageDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_tpl_page');

        $cacheCmsTreeNodeDataAccessDefinition = $container->getDefinition('chameleon_system_core.cache_data_access_cms_tree_node');
        $cacheCmsTreeNodeDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_tree_node');
    }

    private function addRequestCacheConfig(ContainerBuilder $container): void
    {
        $requestCacheRoutingUtilDefinition = $container->getDefinition('chameleon_system_core.util.request_cache_routing');
        $requestCacheRoutingUtilDefinition->setDecoratedService('chameleon_system_core.util.routing');

        $requestCacheCmsPortalDomainsDataAccessDefinition = $container->getDefinition('chameleon_system_core.data_access_cms_portal_domains_request_level_cache_decorator');
        $requestCacheCmsPortalDomainsDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_portal_domains');

        $requestCacheCmsPortalSystemPageDataAccessDefinition = $container->getDefinition('chameleon_system_core.request_cache_data_access_cms_portal_system_page');
        $requestCacheCmsPortalSystemPageDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_portal_system_page');

        $requestCacheCmsTplPageDataAccessDefinition = $container->getDefinition('chameleon_system_core.request_cache_data_access_cms_tpl_page');
        $requestCacheCmsTplPageDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_tpl_page');

        $requestCacheCmsTreeNodeDataAccessDefinition = $container->getDefinition('chameleon_system_core.request_cache_data_access_cms_tree_node');
        $requestCacheCmsTreeNodeDataAccessDefinition->setDecoratedService('chameleon_system_core.data_access_cms_tree_node');
    }

    private function addMailerConfig(array $mailerConfig, ContainerBuilder $container): void
    {
        if (false === $container->hasDefinition('chameleon_system_core.mailer')) {
            return;
        }
        $mailerDefinition = $container->getDefinition('chameleon_system_core.mailer');

        $mailerDefinition->addMethodCall('setSmtpHost', array($mailerConfig['host']));
        $mailerDefinition->addMethodCall('setSmtpUser', array($mailerConfig['user']));
        $mailerDefinition->addMethodCall('setSmtpPassword', array($mailerConfig['password']));

        if ('permissive' === $mailerConfig['peer_security']) {
            $security = 'chameleon_system_core.security.mail.permissive_mail_peer_security';
        } else {
            $security = 'chameleon_system_core.security.mail.strict_mail_peer_security';
        }

        $container->setAlias('chameleon_system_core.security.mail.mail_peer_security', $security);
    }

    private function addBackendConfig(array $backendConfig, ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('chameleon_system_core.backend_controller');
        $definition->addMethodCall('setHomePagedef', [$backendConfig['home_pagedef']]);

        $container->setParameter('chameleon_system.core.export_memory', $backendConfig['export_memory']); // make available for non-DI-dependencies
    }

    /**
     * @param array            $googleApiConfig
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function addGoogleApiConfig(array $googleApiConfig, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('chameleon_system_core.service.google_api_key');
        $args = $definition->getArguments();
        $args[0] = $googleApiConfig['api_key'];
        $definition->setArguments($args);
    }

    /**
     * @param array            $moduleExecutionConfig
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function addModuleExecutionConfig(array $moduleExecutionConfig, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('chameleon_system_core.moduleloader');
        $definition->replaceArgument(5, $container->getDefinition('chameleon_system_core.module_execution_strategy.'.$moduleExecutionConfig['strategy']));
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function addResources(ContainerBuilder $container)
    {
        $container->addResource(new FileResource(PATH_CORE_CONFIG.'/config.inc.php'));
        $container->addResource(new FileResource(PATH_CORE_CONFIG.'/const.inc.php'));
        $container->addResource(new FileResource(PATH_CORE_CONFIG.'/defaults.inc.php'));

        $customerFrameworkConfig = PATH_CUSTOMER_FRAMEWORK.'/config/config.inc.php';
        if (file_exists($customerFrameworkConfig)) {
            $container->addResource(new FileResource($customerFrameworkConfig));
        }
        $customerProjectConfig = PATH_PROJECT_CONFIG.'/config.inc.php';
        if (file_exists($customerProjectConfig)) {
            $container->addResource(new FileResource($customerProjectConfig));
        }
    }

    /**
     * @return void
     */
    private function configureSession(ContainerBuilder $container)
    {
        if (false === SECURITY_REGENERATE_SESSION_ON_USER_CHANGE) {
            return;
        }
        if (false === $container->hasDefinition('chameleon_system_core.event_listener.migrate_session_listener')) {
            return;
        }

        $definition = $container->getDefinition('chameleon_system_core.event_listener.migrate_session_listener');
        $definition->addTag('kernel.event_listener', [
            'event' => CoreEvents::BACKEND_LOGIN_SUCCESS,
            'method' => 'migrateSession',
        ]);
        $definition->addTag('kernel.event_listener', [
            'event' => CoreEvents::BACKEND_LOGOUT_SUCCESS,
            'method' => 'migrateSession',
        ]);
    }
}
