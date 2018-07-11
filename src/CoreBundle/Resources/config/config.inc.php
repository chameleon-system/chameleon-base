<?php

if (!defined('_FRAMEWORK_CONFIG_LOADED')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('DEBUG_LEVEL', 1);

    /**
     * we set this so that this config is called only once.
     */
    define('_FRAMEWORK_CONFIG_LOADED', true);

    /**
     * CMS backend title bar text
     * feel free to overwrite this in your config
     * CMS_VERSION_MAJOR and MINOR is added in MTHeader module to the backend title if logged in.
     */
    if (!defined('CMS_BACKEND_TITLE')) {
        define('CMS_BACKEND_TITLE', 'Chameleon CMS ');
    }

    /**
     * points to the base of the framework layout templates.
     */
    define('PATH_LAYOUTTEMPLATES', _CMS_CORE.'/rendering/layouts/');
    /**
     * points to the base of the custom framework layout templates.
     */
    define('PATH_LAYOUTTEMPLATES_CUSTOM', _CMS_CUSTOM_CORE.'/rendering/layouts/');
    /**
     * points to the base of the customer framework layout templates.
     */
    define('PATH_LAYOUTTEMPLATES_CUSTOMER', _CMS_CUSTOMER_CORE.'/rendering/layouts/');
    /**
     * Points to the framework pagedefinition files.
     */
    define('PATH_PAGE_DEFINITIONS', __DIR__.'/../BackendPageDefs/');
    /**
     * Points to the framework custom pagedefinition files.
     */
    define('PATH_PAGE_DEFINITIONS_CUSTOM', _CMS_CUSTOM_CORE.'/rendering/pages/');
    /**
     * Points to the framework customer pagedefinition files.
     */
    define('PATH_PAGE_DEFINITIONS_CUSTOMER', _CMS_CUSTOMER_CORE.'/rendering/pages/');

    /**
     * Points to the base of the framework Modules.
     */
    define('PATH_MODULES', _CMS_CORE.'/modules/');

    /**
     * Points to the base of the custom framework Modules.
     */
    define('PATH_MODULES_CUSTOM', _CMS_CUSTOM_CORE.'/modules/');

    /**
     * Points to the base of the customer cms extensions.
     */
    define('PATH_MODULES_CUSTOMER', _CMS_CUSTOMER_CORE.'/modules/');

    /**
     * Points to the base of the customer framework Modules.
     */
    define('PATH_CUSTOMER_FRAMEWORK_MODULES', PATH_CUSTOMER_FRAMEWORK.'/modules/');

    /**
     * Points to the module path in the CMS core.
     */
    define('PATH_CORE_MODULES', _CMS_CORE.'/modules/');

    /**
     * Points to the core of the framework.
     */
    define('PATH_CORE', _CMS_CORE.'/core/');

    /**
     * Points to the custom core of the framework.
     */
    define('PATH_CORE_CUSTOM', _CMS_CUSTOM_CORE.'/core/');

    /**
     * Points to the core of the framework.
     */
    define('PATH_CORE_CUSTOMER', _CMS_CUSTOMER_CORE.'/core/');

    define('PATH_ROOT', _CMS_CORE);

    /**
     * points to the HTTP root of the framework.
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    define('HTTP_PATH_ROOT', realpath(_CMS_CORE.'/../../../httpdocs/chameleon/blackbox'));

    /**
     * Points to the library base. Below this path are usually things like ./classes and ./functions.
     */
    define('PATH_LIBRARY', _CMS_CORE.'/library/');

    /**
     * holds the central custom extensions for classes and functions.
     */
    define('PATH_LIBRARY_CUSTOM', _CMS_CUSTOM_CORE.'/library/');

    /**
     * holds the customer extensions for classes and functions for a specific domain.
     */
    define('PATH_LIBRARY_CUSTOMER', _CMS_CUSTOMER_CORE.'/library/');

    if (!defined('IMAGE_RENDERING_RESPONSIVE')) {
        define('IMAGE_RENDERING_RESPONSIVE', true);
    }

    if (!defined('IMAGE_RENDERING_RESPONSIVE_TABLET_SCREEN_SIZE')) {
        define('IMAGE_RENDERING_RESPONSIVE_TABLET_SCREEN_SIZE', 800);
    }

    if (!defined('IMAGE_RENDERING_RESPONSIVE_MOBILE_SCREEN_SIZE')) {
        define('IMAGE_RENDERING_RESPONSIVE_MOBILE_SCREEN_SIZE', 500);
    }
}
