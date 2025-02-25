<?php

namespace ChameleonSystem\CmsDashboardBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ChameleonSystemCmsDashboardExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $dashboardCacheServiceDefinition = $container->getDefinition('chameleon_system_cms_dashboard.bridge_chameleon_service.dashboard_cache_service');
        $dashboardCacheServiceDefinition->setArgument(3, $config['cache_ttl']);

        $searchConsoleServiceDefinition = $container->getDefinition('chameleon_system_cms_dashboard.service.google_search_console_service');
        $searchConsoleServiceDefinition->setArgument(1, $config['google_search_console_auth_json']);

        $searchConsoleWidgetDefinition = $container->getDefinition('chameleon_system_cms_dashboard.bridge_chameleon_dashboard_widgets.search_console_widget');
        $searchConsoleWidgetDefinition->setArgument(5, $config['google_search_console_auth_json']);
        $searchConsoleWidgetDefinition->setArgument(6, $config['google_search_console_domain_property']);
        $searchConsoleWidgetDefinition->setArgument(7, $config['google_search_console_period_days']);

        $searchConsoleServiceDefinition = $container->getDefinition('chameleon_system_cms_dashboard.service.google_analytics_dashboard_service');
        $searchConsoleServiceDefinition->setArgument(1, $config['google_search_console_auth_json']);
        $searchConsoleServiceDefinition->setArgument(2, $config['google_search_console_domain_property']);

        $this->loadGoogleAnalyticsWidgets($config, $container);
    }

    private function loadGoogleAnalyticsWidgets(array $config, ContainerBuilder $container): void{
        $googleAnalyticsWidget = [
            'chameleon_system_cms_dashboard.bridge_chameleon_dashboard_widgets.google_analytics.engagement_rate_widget',
            'chameleon_system_cms_dashboard.bridge_chameleon_dashboard_widgets.google_analytics.geo_location_widget',
            'chameleon_system_cms_dashboard.bridge_chameleon_dashboard_widgets.google_analytics.traffic_source_widget',
            'chameleon_system_cms_dashboard.bridge_chameleon_dashboard_widgets.google_analytics.utm_tracking_widget',
            'chameleon_system_cms_dashboard.bridge_chameleon_dashboard_widgets.google_analytics.e_commerce_widget',
            'chameleon_system_cms_dashboard.bridge_chameleon_dashboard_widgets.google_analytics.device_ratio_widget'
        ];

        foreach($googleAnalyticsWidget as $widgetId){
            $widgetDefinition = $container->getDefinition($widgetId);
            $widgetDefinition->setArgument(5, $config['google_search_console_auth_json']);
            $widgetDefinition->setArgument(6, $config['google_search_console_domain_property']);
            $widgetDefinition->setArgument(7, $config['google_search_console_period_days']);
        }
    }
}
