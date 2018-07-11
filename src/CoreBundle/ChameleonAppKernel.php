<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle;

use Symfony\Component\HttpKernel\Kernel;

abstract class ChameleonAppKernel extends Kernel
{
    /**
     * @var string[]
     */
    private $constants = array(
        'chameleon_system_core.cache.allow' => '_CONFIG_ALLOW_CACHING',
        'chameleon_system_core.cache.cache_less_files' => 'CHAMELEON_LESS_CACHE_LESS_FILES',
        'chameleon_system_core.cache.default_max_age_in_seconds' => 'CMS_CORE_CACHE_DEFAULT_MAX_AGE_IN_SECONDS',
        'chameleon_system_core.cache.max_cache_item_live_time_in_seconds' => 'CHAMELEON_MAX_CACHE_ITEM_LIVE_TIME_IN_SECONDS',
        'chameleon_system_core.cache.memcache_activate' => 'CHAMELEON_MEMCACHE_ACTIVATE',
        'chameleon_system_core.cache.memcache_server1' => 'CHAMELEON_MEMCACHE_SERVER',
        'chameleon_system_core.cache.memcache_port1' => 'CHAMELEON_MEMCACHE_SERVER_PORT',
        'chameleon_system_core.cache.memcache_server2' => 'CHAMELEON_MEMCACHE_SERVER2',
        'chameleon_system_core.cache.memcache_port2' => 'CHAMELEON_MEMCACHE_SERVER_PORT2',
        'chameleon_system_core.cache.memcache_class' => 'CHAMELEON_MEMCACHE_CLASS',
        'chameleon_system_core.cache.memcache_use_fallback' => 'CHAMELEON_MEMCACHE_USE_FALLBACK',
        'chameleon_system_core.cache.memcache_use_lazy_cache_write' => 'CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE',
        'chameleon_system_core.cache.memcache_max_cache_item_live_time_in_seconds' => 'CHAMELEON_MEMCACHE_MAX_CACHE_ITEM_LIVE_TIME_IN_SECONDS',
        'chameleon_system_core.cache.memcache_sessions_server1' => 'CHAMELEON_MEMCACHE_SESSIONS_SERVER',
        'chameleon_system_core.cache.memcache_sessions_server2' => 'CHAMELEON_MEMCACHE_SESSIONS_SERVER_2',
        'chameleon_system_core.cache.memcache_sessions_port1' => 'CHAMELEON_MEMCACHE_SESSIONS_SERVER_PORT',
        'chameleon_system_core.cache.memcache_sessions_port2' => 'CHAMELEON_MEMCACHE_SESSIONS_SERVER_PORT_2',
        'chameleon_system_core.cache.memcache_sessions_use_native_driver' => 'CMS_SESSION_USE_MEMCACHE_NATIVE_DRIVER',
        'chameleon_system_core.cache.include_cache_delete_trace_info' => 'CHAMELEON_CACHE_INCLUDE_CACHE_DELETE_TRACE_INFO',
        'chameleon_system_core.cache.use_file_system_as_standard_cache' => 'CHAMELEON_CACHE_USE_FILE_SYSTEM_AS_STANDARD_CACHE',
        'chameleon_system_core.cache.memcached_timeout_in_milliseconds' => 'CHAMELEON_MEMCACHED_TIMEOUT_IN_MILLISECONDS',

        'chameleon_system_core.debug.debug_last_order' => 'CHAMELEON_DEBUG_LAST_ORDER',
        'chameleon_system_core.debug.show_view_source_html_hints' => 'DEBUG_SHOW_VIEW_SOURCE_HTML_HINTS',
        'chameleon_system_core.debug.print_module_render_time' => 'CHAMELEON_DEBUG_PRINT_MODULE_RENDER_TIME',
        'chameleon_system_core.debug.cms_output_page_load_time_info' => 'CMS_OUTPUT_PAGE_LOAD_TIME_INFO',
        'chameleon_system_core.debug.cms_debug_cache_keys' => 'CMS_DEBUG_CACHE_KEYS',
        'chameleon_system_core.debug.cms_debug_cache_record' => 'CMS_DEBUG_CACHE_RECORD',
        'chameleon_system_core.debug.cms_debug_cache_recordlist' => 'CMS_DEBUG_CACHE_RECORDLIST',

        'chameleon_system_core.request.trusted_proxies' => 'REQUEST_TRUSTED_PROXIES',
        'chameleon_system_core.request.trusted_header_client_ip' => 'REQUEST_TRUSTED_HEADER_CLIENT_IP',
        'chameleon_system_core.request.trusted_header_client_host' => 'REQUEST_TRUSTED_HEADER_CLIENT_HOST',
        'chameleon_system_core.request.trusted_header_client_port' => 'REQUEST_TRUSTED_HEADER_CLIENT_PORT',
        'chameleon_system_core.request.trusted_header_client_protocol' => 'REQUEST_TRUSTED_HEADER_CLIENT_PROTO',

        'chameleon_system_core.resources.enable_external_resource_collection' => 'ENABLE_EXTERNAL_RESOURCE_COLLECTION',
        'chameleon_system_core.resources.enable_external_resource_collection_refresh_prefix' => 'ENABLE_EXTERNAL_RESOURCE_COLLECTION_REFRESH_PREFIX',
        'chameleon_system_core.resources.enable_external_resource_collection_minify' => 'ENABLE_EXTERNAL_RESOURCE_COLLECTION_MINIFY',

        'chameleon_system_core.allow_database_logging' => 'ALLOW_DATABASELOGGING',
        'chameleon_system_core.development_email' => '_DEVELOPMENT_EMAIL',
        'chameleon_system_core.cms_log_level' => 'CMS_LOG_LEVEL',
        'database_host' => '_CUSTOMER_SERVER_DBHOST',
        'database_name' => '_CUSTOMER_SERVER_DBNAME',
        'database_user' => '_CUSTOMER_SERVER_DBUSER',
        'database_password' => '_CUSTOMER_SERVER_DBPWD',

        'chameleon_system_external_tracker.demo_mode' => 'CHAMELEON_PKG_EXTERNAL_TRACKER_DEMO_MODE',

        'chameleon_system_external_tracker_google_analytics.enable_campaign_tracking' => 'CHAMELEON_PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CAMPAIGN_TRACKING',
    );

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        parent::boot();

        ServiceLocator::setContainer($this->container);
        $this->booted = true;
        $this->defineConstants();
    }

    private function defineConstants()
    {
        foreach ($this->constants as $param => $const) {
            if (!defined($const)) {
                define($const, $this->container->getParameter($param));
            }
        }
    }
}
