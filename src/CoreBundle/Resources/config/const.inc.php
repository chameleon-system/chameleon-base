<?php

if (!defined('PATH_WEB')) {
    define('PATH_WEB', $_SERVER['DOCUMENT_ROOT']);
}
define('PATH_PROJECT_BASE', PATH_WEB.'/../');
define('PATH_PROJECT_CONFIG', PATH_PROJECT_BASE.'/config/');
/**
 * @deprecated since 6.2.0 - the vendor path should no longer be used. Use the bundle syntax to refer to concrete bundles instead.
 */
define('PATH_VENDORS', PATH_PROJECT_BASE.'/vendor/');
/**
 * @deprecated since 6.2.0 - Chameleon packages should be treated like any third-party bundle.
 */
define('ESONO_PACKAGES', PATH_VENDORS.'/chameleon-system/');
/**
 * @deprecated since 6.2.0 - Chameleon packages should be treated like any third-party bundle.
 */
define('PATH_CORE_BASE', PATH_VENDORS.'/chameleon-system/core/');
/**
 * @deprecated since 6.2.0 - Chameleon packages should be treated like any third-party bundle.
 */
define('CHAMELEON_CORE_COMPONENTS', PATH_CORE_BASE.'/Components/');
define('PATH_CORE_CONFIG', __DIR__.'/');
define('PATH_CORE_VIEWS', __DIR__.'/../views/');

/**
 * the core cms files (controller, etc...).
 */
define('_CMS_CORE', dirname(dirname(__DIR__)).'/private/');
/**
 * path to the central extensions for the customer.
 *
 * @deprecated since 6.2.0 - custom core is no longer supported. Use bundles instead.
 */
define('_CMS_CUSTOM_CORE', ESONO_PACKAGES.'/custom-core/');
/**
 * path to the extensions specific to one domain.
 */
define('_CMS_CUSTOMER_CORE', PATH_PROJECT_BASE.'/src/extensions/');
/**
 * customer framework rootpath.
 */
define('PATH_CUSTOMER_FRAMEWORK', PATH_PROJECT_BASE.'/src/framework/');
/**
 * path to the customer pagelayouts
 * trailing slash "/" needed.
 */
define('PATH_CUSTOMER_PAGELAYOUTS', PATH_CUSTOMER_FRAMEWORK.'/layoutTemplates/');
/**
 * the core cms files (controller, etc...).
 *
 * @deprecated since 6.2.0 - no longer used.
 */
define('_CMS_CORE_ENGINE', _CMS_CORE.'/core');
/**
 * path to the customer pagedefinitions
 * trailing slash "/" needed.
 */
define('PATH_CUSTOMER_PAGEDEFINITIONS', PATH_CUSTOMER_FRAMEWORK.'/pageDefinitions/');
