<?php

/**
 * -------------------------------------------------------------------------------------------------------------------
 * PATHS AND URLS
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * relative path to framework controller. The constant MUST be prefixed with a slash.
 * This value should normally not be changed.
 */
define('PATH_CUSTOMER_FRAMEWORK_CONTROLLER', '/');

/**
 * URL frontend front controller.
 */
if (!defined('URL_WEB_CONTROLLER')) {
    define('URL_WEB_CONTROLLER', REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].PATH_CUSTOMER_FRAMEWORK_CONTROLLER);
}

/**
 * URL backend front controller.
 */
if (!defined('PATH_CMS_CONTROLLER')) {
    define('PATH_CMS_CONTROLLER', '/cms');
}

/**
 * URL backend front controller.
 */
if (!defined('URL_CMS_CONTROLLER')) {
    define('URL_CMS_CONTROLLER', REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].PATH_CMS_CONTROLLER);
}

/**
 * CMS URL.
 */
if (!defined('URL_CMS')) {
    define('URL_CMS', '/chameleon/blackbox/');
}

/**
 * location of update files.
 */
if (!defined('PATH_CMS_UPDATE')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('PATH_CMS_UPDATE', PATH_CORE_BASE.'/private/engine/updates/');
}

/**
 * CMS customer data path.
 */
if (!defined('PATH_CMS_CUSTOMER_DATA')) {
    define('PATH_CMS_CUSTOMER_DATA', PATH_PROJECT_BASE.'/app/cmsdata/');
}

if (!defined('PATH_CACHE')) {
    define('PATH_CACHE', PATH_PROJECT_BASE.'/app/cache/');
}
/**
 * path to the customer pagelayouts
 * trailing slash "/" needed.
 */
if (!defined('PATH_CUSTOMER_PAGEMASTERDEFINITIONS')) {
    define('PATH_CUSTOMER_PAGEMASTERDEFINITIONS', PATH_CUSTOMER_FRAMEWORK.'/masterPageDefinitions/');
}

/**
 * the url to the user chameleon http files.
 */
if (!defined('URL_USER_CMS_PUBLIC')) {
    define('URL_USER_CMS_PUBLIC', REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].'/chameleon/');
}
/**
 * the path to the user chameleon http files.
 */
if (!defined('PATH_USER_CMS_PUBLIC')) {
    define('PATH_USER_CMS_PUBLIC', realpath($_SERVER['DOCUMENT_ROOT'].'/chameleon/'));
}

/**
 * path to media files (images and videos)
 * trailing slash "/" needed.
 */
if (!defined('PATH_MEDIA_LIBRARY')) {
    define('PATH_MEDIA_LIBRARY', PATH_USER_CMS_PUBLIC.'/mediapool/');
}

/**
 * Path to filetype icons.
 */
if (!defined('PATH_FILETYPE_ICONS')) {
    define('PATH_FILETYPE_ICONS', PATH_USER_CMS_PUBLIC.'/blackbox/images/filetype_icons/');
}
/**
 * Path to low-quality filetype icons.
 */
if (!defined('PATH_FILETYPE_ICONS_LOW_QUALITY')) {
    define('PATH_FILETYPE_ICONS_LOW_QUALITY', PATH_FILETYPE_ICONS.'16x16/');
}
/**
 * URL path to filetype icons relative to TGlobal::GetStaticURLToWebLib().
 */
if (!defined('URL_FILETYPE_ICONS')) {
    define('URL_FILETYPE_ICONS', '/images/filetype_icons/');
}
/**
 * URL path to low-quality filetype icons relative to TGlobal::GetStaticURLToWebLib().
 */
if (!defined('URL_FILETYPE_ICONS_LOW_QUALITY')) {
    define('URL_FILETYPE_ICONS_LOW_QUALITY', URL_FILETYPE_ICONS.'16x16/');
}

/**
 * url path to media files (images and videos)
 * trailing slash "/" needed
 * example: /chameleon/mediapool/.
 */
if (!defined('URL_MEDIA_LIBRARY_PATH')) {
    define('URL_MEDIA_LIBRARY_PATH', '/chameleon/mediapool/');
}

/**
 * path to media thumbnail files (images and videos)
 * trailing slash "/" needed.
 */
if (!defined('PATH_MEDIA_LIBRARY_THUMBS')) {
    define('PATH_MEDIA_LIBRARY_THUMBS', PATH_MEDIA_LIBRARY.'thumbs/');
}

/**
 * url to media files (images and videos)
 * trailing slash "/" needed.
 */
if (!defined('URL_MEDIA_LIBRARY')) {
    define('URL_MEDIA_LIBRARY', URL_USER_CMS_PUBLIC.'mediapool/');
}

/**
 * url to media thumbnail files (images and videos)
 * trailing slash "/" needed.
 */
if (!defined('URL_MEDIA_LIBRARY_THUMBS')) {
    define('URL_MEDIA_LIBRARY_THUMBS', URL_MEDIA_LIBRARY.'thumbs/');
}

/**
 * the url to the outbox (location for all outgoing file transfers.
 */
if (!defined('URL_OUTBOX')) {
    define('URL_OUTBOX', URL_USER_CMS_PUBLIC.'outbox/');
}

/**
 * the url to the outbox for protected documents.
 */
if (!defined('URL_PROTECTED_DOCUMENT_VIRTUAL_OUTBOX')) {
    define('URL_PROTECTED_DOCUMENT_VIRTUAL_OUTBOX', URL_USER_CMS_PUBLIC.'downloads/');
}

if (!defined('CHAMELEON_CHECK_VALID_USER_SESSION_ON_PROTECTED_DOWNLOADS')) {
    define('CHAMELEON_CHECK_VALID_USER_SESSION_ON_PROTECTED_DOWNLOADS', true);
}

/**
 * the url to the outbox for public documents. Is only used if defined CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS
 * is true.
 */
if (!defined('URL_DOCUMENT_VIRTUAL_OUTBOX')) {
    define('URL_DOCUMENT_VIRTUAL_OUTBOX', URL_USER_CMS_PUBLIC.'public/');
}

/**
 * the absolute path on the filesystem to the outbox.
 */
if (!defined('PATH_OUTBOX')) {
    define('PATH_OUTBOX', PATH_USER_CMS_PUBLIC.'/outbox/');
}

/**
 * Path for font files.
 */
if (!defined('PATH_CMS_FONTS')) {
    /**
     * @deprecated
     */
    define('PATH_CMS_FONTS', PATH_CUSTOMER_FRAMEWORK.'/fonts/');
}

/**
 * tmp folder for chameleon to store process files. make sure the folder is writable and has enough space.
 */
if (!defined('CMS_TMP_DIR')) {
    define('CMS_TMP_DIR', PATH_CMS_CUSTOMER_DATA.'/tmp/');
}

/**
 * URL/hostname of content delivery network or server for static content e.g. lighttpd
 * if you need different urls by filetype, path or whatever extend the method TGlobal::GetStaticURL()
 * note: you may also set several static server separated  by comma - in this case the static request on a page will be evenly distributed across a page.
 */
if (!defined('URL_STATIC')) {
    define('URL_STATIC', REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST']);
}

/*
 * defines the media files import directory
*/
if (!defined('PATH_MEDIA_LOCAL_IMPORT_FOLDER')) {
    define('PATH_MEDIA_LOCAL_IMPORT_FOLDER', PATH_CMS_CUSTOMER_DATA.'/mediaImport/');
}

/*
 * defines the document files import directory
*/
if (!defined('PATH_DOCUMENT_LOCAL_IMPORT_FOLDER')) {
    define('PATH_DOCUMENT_LOCAL_IMPORT_FOLDER', PATH_CMS_CUSTOMER_DATA.'/documentImport/');
}

/**
 * url path to thumb media files (images and videos)
 * trailing slash "/" needed
 * example: /chameleon/mediapool/thumbs.
 */
if (!defined('URL_MEDIA_LIBRARY_THUMBS_PATH')) {
    define('URL_MEDIA_LIBRARY_THUMBS_PATH', '/chameleon/mediapool/thumbs/');
}

/**
 * absolute path (base is document root) to small 404 error image (default is an 50px PNG image).
 */
if (!defined('CHAMELEON_404_IMAGE_PATH_SMALL')) {
    define('CHAMELEON_404_IMAGE_PATH_SMALL', '/chameleon/blackbox/images/noImage/noImage_50.jpg');
}

/**
 * absolute path (base is document root) to small 404 error image (default is an 400px PNG image).
 */
if (!defined('CHAMELEON_404_IMAGE_PATH_BIG')) {
    define('CHAMELEON_404_IMAGE_PATH_BIG', '/chameleon/blackbox/images/noImage/noImage_400.jpg');
}
/**
 * url path to frontend jquery.
 */
if (!defined('CHAMELEON_URL_JQUERY')) {
    define('CHAMELEON_URL_JQUERY', '/static/js/jquery.js');
}
/**
 * if you want to use jquery from google apis, you can set the path using this constant. CHAMELEON_URL_JQUERY will still be used if jquery via google can not be reached.
 */
if (!defined('CHAMELEON_URL_GOOGLE_JQUERY')) {
    define('CHAMELEON_URL_GOOGLE_JQUERY', false);
}

/**
 * path to themes directory.
 */
if (!defined('CHAMELEON_PATH_THEMES')) {
    define('CHAMELEON_PATH_THEMES', PATH_PROJECT_BASE.'/src/themes/');
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * WORKFLOW AND REVISIONS
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * if set to true, then the workflow engine is enabled.
 */
if (!defined('_CONFIG_USE_WORKFLOW_ENGINE')) {
    /**
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    define('_CONFIG_USE_WORKFLOW_ENGINE', false);
}

/**
 * CMS workflow temp dir for uploaded media files.
 */
if (!defined('PATH_CMS_CUSTOMER_WORKFLOW_MEDIA')) {
    /**
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    define('PATH_CMS_CUSTOMER_WORKFLOW_MEDIA', PATH_CMS_CUSTOMER_DATA.'/workflow/media/');
}

/**
 * CMS workflow temp dir for uploaded media file thumbnails.
 */
if (!defined('PATH_CMS_CUSTOMER_WORKFLOW_MEDIA_THUMBS')) {
    /**
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    define('PATH_CMS_CUSTOMER_WORKFLOW_MEDIA_THUMBS', PATH_CMS_CUSTOMER_DATA.'/workflow/media/thumbs/');
}

/**
 * CMS workflow temp dir for uploaded documents.
 */
if (!defined('PATH_CMS_CUSTOMER_WORKFLOW_DOCUMENT')) {
    /**
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    define('PATH_CMS_CUSTOMER_WORKFLOW_DOCUMENT', PATH_CMS_CUSTOMER_DATA.'/workflow/documents/');
}

/*
 * Set to true if you want to see tasks in cms
 * note: needs to be enabled for workflow
 */
if (!defined('CMS_SHOW_TASKS')) {
    /**
     * @deprecated since 6.2.0 - dashboard widgets are not supported anymore
     */
    define('CMS_SHOW_TASKS', true);
}

/*
 * Set to true if you want to see information tasks in cms
 * note: needs to be enabled for workflow
 */
if (!defined('CMS_SHOW_TASKS_INFORMATION')) {
    /**
     * @deprecated since 6.2.0 - dashboard widgets are not supported anymore
     */
    define('CMS_SHOW_TASKS_INFORMATION', false);
}

/*
* set this to true to activate record revision management in CMS backend
* you need to set revision management active for every table manually in table configuration also
*/
if (!defined('CMS_ACTIVE_REVISION_MANAGEMENT')) {
    /**
     * @deprecated since 6.3.0
     */
    define('CMS_ACTIVE_REVISION_MANAGEMENT', false);
}

/*
 * if true a revision will be created for all pages of a workflow transaction if revision management is active
*/
if (!defined('CREATE_PAGE_REVISION_ON_WORKFLOW_PUBLISH')) {
    /**
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    define('CREATE_PAGE_REVISION_ON_WORKFLOW_PUBLISH', false);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * SECURITY
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * force the use of cookies in the website frontend?
 */
if (!defined('USE_ONLY_COOKIES_FOR_SESSION_ID')) {
    /**
     * @deprecated since 6.2.0 - this feature will be removed for security reasons. Projects should always use cookies
     * and not change this value.
     */
    define('USE_ONLY_COOKIES_FOR_SESSION_ID', true);
}

/**
 * safe php directories (these may be accessed via include/require
 * to specify more than one directory, you need to separate them via ";".
 */
if (!defined('_CMS_BASE_DIR_RESTRICTION')) {
    define('_CMS_BASE_DIR_RESTRICTION', $_SERVER['DOCUMENT_ROOT'].'/../..;'.$_SERVER['DOCUMENT_ROOT'].'/themes');
}

/*
* these parameters will be removed from any request... the cms will cause a 301 redirect without them to the original page
* this prevents search engines from indexing the same page more than once
*/
if (!defined('INVALID_GET_PARAMS')) {
    define('INVALID_GET_PARAMS', '');
}

/**
 * change the session ID on extranet login/logout.
 */
if (!defined('SECURITY_REGENERATE_SESSION_ON_USER_CHANGE')) {
    define('SECURITY_REGENERATE_SESSION_ON_USER_CHANGE', true);
}

/*
* force a check for an install folder in productive mode and prevent the cms from running if found
* if you have such a folder that you want to keep, you can disable the check by setting this constant to false
*/
if (!defined('CMS_INSTALL_FOLDER_CHECK')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('CMS_INSTALL_FOLDER_CHECK', true);
}

/*
 * if true non admins will not be able to login while open locks for the user are present
*/
if (!defined('PREVENT_PARALLEL_LOGINS_FOR_NON_ADMINS')) {
    define('PREVENT_PARALLEL_LOGINS_FOR_NON_ADMINS', false);
}

/**
 * the filter to use for ALL input (front end only) possible options: see constants in TCMSUserInput.
 */
if (!defined('TCMSUSERINPUT_DEFAULTFILTER')) {
    define('TCMSUSERINPUT_DEFAULTFILTER', 'TCMSUserInput_BaseText');
}

/**
 * if set to true, cookie interaction is reduced to http only giving higher security to prevent XSS attacks
 * this is supported as of PHP 5.2.
 */
if (!defined('CMS_COOKIE_HTTP_ONLY')) {
    /**
     * @deprecated since 6.0.12 - use the Symfony FrameworkBundle's framework: session: cookie_httponly option instead.
     */
    define('CMS_COOKIE_HTTP_ONLY', false);
}

/**
 * protect all module_fnc calls via a random salt.
 */
if (!defined('CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN')) {
    define('CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN', true);
}

/**
 * enable protection in cms backend.
 */
if (!defined('CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN_IN_BACKEND')) {
    define('CMS_PROTECT_ALL_MODULE_FNC_CALLS_USING_TOKEN_IN_BACKEND', true);
}

/**
 * if enabled (default is disabled) the extranet user session adds the IP address to the session key.
 *
 * note: this may lead to problems with some non-sticky client side proxy/load balancers like AOL uses them,
 * because a user uses more than one IP address per session.
 *
 * @see http://technicallyeasy.net/2007/09/aol-users-multiple-ip-addresses/
 */
if (!defined('CHAMELEON_SECURITY_EXTRANET_SESSION_USE_IP_IN_KEY')) {
    define('CHAMELEON_SECURITY_EXTRANET_SESSION_USE_IP_IN_KEY', false);
}

/**
 * If enabled, the user agent (web browser) will be added to the session key, making session hijacking more difficult.
 *
 * note: you can`t activate this if you use a connector with user sessions, so the session is used by the user and a web process connect at once.
 * this would lead to different user agents per session
 */
if (!defined('CHAMELEON_SECURITY_EXTRANET_SESSION_USE_USER_AGENT_IN_KEY')) {
    define('CHAMELEON_SECURITY_EXTRANET_SESSION_USE_USER_AGENT_IN_KEY', true);
}

/**
 * defines the number of minutes a document_authority token is valid.
 */
if (!defined('CHAMELEON_DOCUMENT_AUTH_TOKEN_LIFE_TIME_IN_MINUTES')) {
    define('CHAMELEON_DOCUMENT_AUTH_TOKEN_LIFE_TIME_IN_MINUTES', 60);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * SEO
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * if you want to activate SEO urls for images, activate this option. the system will create all
 * categories as directories in PATH_MEDIA_LIBRARY. every directory will contain "i" as symlink to PATH_MEDIA_LIBRARY
 * Image URLs will be generated in the form: /category/path/to/image/i/imagename.jpg
 * IMPORTANT: when activating this, you MUST create a symlink in your webroot to point to PATH_MEDIA_LIBRARY/ROOT-MEDIA-CATEGORY-NAME
 * if your categories use a multi-lang field for field-name, then you must create one symlink for every root category.
 */
if (!defined('CMS_MEDIA_ENABLE_SEO_URLS')) {
    define('CMS_MEDIA_ENABLE_SEO_URLS', false);
}

/**
 * the absolute path on the filesystem to the outbox.
 */
if (!defined('PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS')) {
    define('PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS', PATH_OUTBOX.'/media-seo');
}

/**
 * auto rewrite urls with "_" to "-" (activate for older websites to support old google links)
 * DO NOT activate this if you use custom SmartURLHandlers or the pkgArticle package, this may kill your id matches in URLs
 * like "_AID_".
 */
if (!defined('CMS_URL_AUTO_REWRITE_UNDERSCORE')) {
    /**
     * @deprecated since 6.1.5 - no longer used.
     */
    define('CMS_URL_AUTO_REWRITE_UNDERSCORE', false);
}

/**
 * Rewrite uppercase URL to lowercase url for example www.mysite.de/MySite gets www.mysite.de/mysite
 * The complete cache including Symfony routing cache need to be deleted after this setting is changed.
 */
if (!defined('CHAMELEON_SEO_URL_REWRITE_TO_LOWERCASE')) {
    define('CHAMELEON_SEO_URL_REWRITE_TO_LOWERCASE', false);
}

/**
 * Clean URL to correct URL for example www.mysite.de///MySite gets www.mysite.de/MySite.
 */
if (!defined('CHAMELEON_SEO_URL_REWRITE_TO_CLEAN')) {
    /**
     * @deprecated since 6.1.6 - no longer used.
     */
    define('CHAMELEON_SEO_URL_REWRITE_TO_CLEAN', true);
}

/**
 * Remove trailing slash from URLs: www.mysite.de/MySite/ => www.mysite.de/MySite
 * The complete cache including Symfony routing cache need to be deleted after this setting is changed.
 */
if (!defined('CHAMELEON_SEO_URL_REMOVE_TRAILING_SLASH')) {
    define('CHAMELEON_SEO_URL_REMOVE_TRAILING_SLASH', false);
}

/**
 * if the pkgUrlAlias package is installed it is possible to activate automatic redirects for public downloads if a
 * document is moved in the CMS backend (this changes the directory ID in the download path)
 * this option makes sure, that these downloads will be found by search engines by redirecting to the new URl.
 */
if (!defined('PKG_URL_ALIAS_ADD_PUBLIC_DOCUMENT_REDIRECTS_ON_MOVE')) {
    define('PKG_URL_ALIAS_ADD_PUBLIC_DOCUMENT_REDIRECTS_ON_MOVE', false);
}

/**
 * if activated SEO Url handler stream public downloads directly. We don not need symlinks anymore.
 * if activated cronjob to update download symlinks was automatically deactivated.
 * if you switch this to true you have to delete manually all symlinks.
 */
if (!defined('CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS')) {
    define('CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS', true);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * DEBUGGING, ERROR HANDLING AND DEVELOPMENT
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * disable firephp by default.
 */
if (!defined('CMS_ENABLE_FIREPHP')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('CMS_ENABLE_FIREPHP', false);
}

/**
 * defile a logfile to which changes in the database should be logged.
 */
if (!defined('PATH_CMS_CHANGE_LOG')) {
    define('PATH_CMS_CHANGE_LOG', PATH_CMS_CUSTOMER_DATA.'/dbchanges');
}

/**
 * set to true if you want to disable warnings about missing images in TCMSImage or ImageMagick class
 * works only in combination with _DEVELOPMENT_MODE = true.
 */
if (!defined('DISABLE_IMAGE_ERRORS_IN_DEVELOPMENT_MODE')) {
    define('DISABLE_IMAGE_ERRORS_IN_DEVELOPMENT_MODE', true);
}

/**
 * enables or disables the default error handler
 * note: if you want to use your own set this to false and include your own error handler e.g. in the index.php and
 *       and dont forget to do: register_shutdown_function(yourerrorhandlingfunction);.
 */
if (!defined('USE_DEFAULT_ERROR_HANDLER')) {
    define('USE_DEFAULT_ERROR_HANDLER', true);
}

/**
 * default error log size of 2GB (in bytes).
 */
if (!defined('MAX_ERROR_LOG_SIZE')) {
    define('MAX_ERROR_LOG_SIZE', 2147483648);
}

/*
 * defines the error log folder used by the chameleon internal error handler
*/
if (!defined('ERROR_LOG_FOLDER')) {
    define('ERROR_LOG_FOLDER', PATH_CMS_CUSTOMER_DATA.'/logs/');
}

/*
 * defines the error log filename used by the chameleon internal error handler in combination with ERROR_LOG_FOLDER
*/
if (!defined('ERROR_LOG_FILE')) {
    define('ERROR_LOG_FILE', 'fatalerror.log');
}

/*
 * defines the fatal error frontside redirect page used by the chameleon internal error handler
 * file is loaded from DOCUMENT_ROOT/filename.php
*/
if (!defined('FATAL_PHP_FILE')) {
    define('FATAL_PHP_FILE', 'fatal.php');
}

/**
 * path and filename of the chameleon error log file.
 */
if (!defined('CMS_LOG_FILE_NAME')) {
    define('CMS_LOG_FILE_NAME', ERROR_LOG_FOLDER.'/chameleon.log');
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * CACHING
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * if set to true, then the TCMSSmartURL handler uses caching
 * disable if you want to use multiple page connections on one tree node with timeframe configuration.
 */
if (!defined('CMS_ACTIVE_SMART_URL_HANDLER_CACHING')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('CMS_ACTIVE_SMART_URL_HANDLER_CACHING', true);
}

/**
 * enable writes to cache info table
 * Attention: set this to false, and cache will only be emptied on flush.
 */
if (!defined('CHAMELEON_CACHE_ENABLE_CACHE_INFO')) {
    define('CHAMELEON_CACHE_ENABLE_CACHE_INFO', true);
}

/**
 * enables Load with caching in all TCMSRecord Objects by default. use TCacheManagerRuntimeCache::EnableAutoCaching(true/false) to enable or disable the option via program code
 * -default to false. there are some caching issues when using this and the first page load after flushing the cache will be very slow due to too many cache writes. so use with caution.
 */
if (!defined('CHAMELEON_CACHING_ENABLE_RUNTIME_PER_DEFAULT')) {
    define('CHAMELEON_CACHING_ENABLE_RUNTIME_PER_DEFAULT', false);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * SESSION HANDLING
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * disable session locking (if db based session) - set to true if you want higher performance (but lower integrity).
 */
if (!defined('DISABLE_SESSION_LOCKING')) {
    define('DISABLE_SESSION_LOCKING', false);
}

/*
* prevent bots from using session locking. since all bots get the same session id, they lock each other if session locking is active.
* setting this to true prevents this from happening
*/
if (!defined('CMS_BOT_DISABLE_SESSION_LOCKING')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('CMS_BOT_DISABLE_SESSION_LOCKING', true);
}

/**
 * use standard file based sessions instead of database storage engine
 * if you want to use the native php memcached session handling you need to set this to true, too.
 */
if (!defined('USE_FILE_BASED_SESSION_HANDLING')) {
    /**
     * @deprecated since 6.2.0 - no longer used. Only memcached and database sessions are supported.
     */
    define('USE_FILE_BASED_SESSION_HANDLING', false);
}

/**
 * how long sessions live.
 */
if (!defined('CMS_MAX_SESSION_LIFETIME')) {
    /**
     * @deprecated since 6.0.12 - use the Symfony FrameworkBundle's framework: session: cookie_lifetime option instead.
     */
    define('CMS_MAX_SESSION_LIFETIME', -1);
}

/**
 * if set to true, then the system will keep the session cookie in the browser for as long CMS_MAX_SESSION_LIFETIME.
 */
if (!defined('CMS_SESSION_KEEP_ACTIVE_ON_BROWER_CLOSE')) {
    /**
     * @deprecated since 6.0.12 - set the Symfony FrameworkBundle's framework: session: cookie_lifetime option to a value
     *                            different than 0 instead.
     */
    define('CMS_SESSION_KEEP_ACTIVE_ON_BROWER_CLOSE', false);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * MULTILANGUAGE SUPPORT
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * activates multilanguage support in the frontend
 * (should be disabled for performance reasons if not needed).
 */
if (!defined('ACTIVE_TRANSLATION')) {
    define('ACTIVE_TRANSLATION', false);
}

/**
 * activates multilanguage support in the CMS backend
 * (should be disabled for performance reasons if not needed).
 */
if (!defined('ACTIVE_BACKEND_TRANSLATION')) {
    define('ACTIVE_BACKEND_TRANSLATION', false);
}

/**
 * active backend translation API.
 */
if (!defined('ACTIVE_BACKEND_TRANSLATION_API')) {
    /**
     * @deprecated since 6.2.0 - translation service is no longer supported.
     */
    define('ACTIVE_BACKEND_TRANSLATION_API', 'NONE');
} // NONE | GOOGLE | MICROSOFT

/**
 * Microsoft Translator (API).
 */
if (!defined('AZURE_CLIENT_ID')) {
    /**
     * @deprecated since 6.2.0 - translation service is no longer supported.
     */
    define('AZURE_CLIENT_ID', '');
}
if (!defined('AZURE_CLIENT_SECRET')) {
    /**
     * @deprecated since 6.2.0 - translation service is no longer supported.
     */
    define('AZURE_CLIENT_SECRET', '');
}

/**
 * if set to true, then translated fields in field based translation will default to the base language if the field is empty
 * that is, the translation is only loaded if not empty. if you set this switch to false, the translation will also be used if empty.
 */
if (!defined('CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE')) {
    define('CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE', true);
}

/**
 * if set to true, then the list manager shows a translation fallback for field based translations if the translation is empty.
 */
if (!defined('CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE_IN_LISTMANAGER')) {
    /**
     * @deprecated since 6.1.2 - the value of CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE will
     *                           now also be used in the list manager.
     */
    define('CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE_IN_LISTMANAGER', true);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * FRONTEND
 * -------------------------------------------------------------------------------------------------------------------.
 */
if (!defined('IMAGE_RENDERING_RESPONSIVE')) {
    define('IMAGE_RENDERING_RESPONSIVE', false);
}

/*
* Set to true if you want to use the default imagelayer (like the light or thickbox)
* you can specify which box should be used with other parameters (e.g. USE_LIGHTBOX)
*/
if (!defined('USE_IMAGELAYER')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('USE_IMAGELAYER', true);
}

/**
 * use lightbox clone instead of thickbox.
 */
if (!defined('USE_LIGHTBOX')) {
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    define('USE_LIGHTBOX', false);
}

/*
* if this isset to true the module loader will automatically render a div around the module content
* e.g. <div id="spot[Spotname]" class="Chameleon[ModuleClassName] Chameleon[ModuleViewName]">[modulecontent]</div>
* IMPORTANT NOTICE: THIS (should at least) ONLY AFFECT FRONTEND MODULES
*/
if (!defined('RENDER_DIV_WITH_MODULE_AND_VIEW_NAME_ON_MODULE_LOAD')) {
    define('RENDER_DIV_WITH_MODULE_AND_VIEW_NAME_ON_MODULE_LOAD', true);
}

/*
* the max width of zoom images on the website
* set CMS_MAX_IMAGE_ZOOM_WIDTH and CMS_MAX_IMAGE_ZOOM_HEIGHT to null if you want to disable the max-zoom size
*/
if (!defined('CMS_MAX_IMAGE_ZOOM_WIDTH')) {
    define('CMS_MAX_IMAGE_ZOOM_WIDTH', 1200);
}

/*
* the max height of zoom images on the website
*/
if (!defined('CMS_MAX_IMAGE_ZOOM_HEIGHT')) {
    define('CMS_MAX_IMAGE_ZOOM_HEIGHT', 800);
}

/*
* sends a UTF8 header for every page generated. If you need to use none utf8 encoding, you must disable it
* here, and then send your own header using header('Content-type: text/html; charset=UTF-8');
*/
if (!defined('CMS_AUTO_SEND_UTF8_HEADER')) {
    define('CMS_AUTO_SEND_UTF8_HEADER', true);
}

/**
 * currently only send in template engine mode to protect the cms against xss attacks from third party domains.
 */
if (!defined('CMS_AUTO_SEND_BACKEND_HEADER_CONTENT_SECURITY_POLICY')) {
    define('CMS_AUTO_SEND_BACKEND_HEADER_CONTENT_SECURITY_POLICY', 'script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' ajax.googleapis.com '.URL_STATIC);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * CHAMELEON SHOP
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * delay used to reduce load during the shop indexing process.
 */
if (!defined('CMS_SHOP_INDEX_LOAD_DELAY_MILLISECONDS')) {
    define('CMS_SHOP_INDEX_LOAD_DELAY_MILLISECONDS', 0);
}

/**
 * track detail views of articles.
 */
if (!defined('CMS_SHOP_TRACK_ARTICLE_DETAIL_VIEWS')) {
    define('CMS_SHOP_TRACK_ARTICLE_DETAIL_VIEWS', true);
}

/*
* the max number of days that items in the shop_order_basket table are kept. we remove rawdata_* after SHOP_ORDER_BASKET_MAX_LOG_AGE_IN_DAYS
* and delete the entry after 2*SHOP_ORDER_BASKET_MAX_LOG_AGE_IN_DAYS
*/
if (!defined('SHOP_ORDER_BASKET_MAX_LOG_AGE_IN_DAYS')) {
    define('SHOP_ORDER_BASKET_MAX_LOG_AGE_IN_DAYS', 30);
}

/*
* allows to redirect V1 Product URLS to the new V2 URLS if V2 is activated via cms config - the default is false
* because it costs alot of performance to do that
*/
if (!defined('AUTOMATICALLY_MAP_SHOP_PRODUCT_V1_URLS_TO_V2_IF_ACTIVE')) {
    define('AUTOMATICALLY_MAP_SHOP_PRODUCT_V1_URLS_TO_V2_IF_ACTIVE', false);
}

/**
 * clears the basket of a user on logout.
 */
if (!defined('SHOP_CLEAR_BASKET_CONTENTS_ON_LOGOUT')) {
    define('SHOP_CLEAR_BASKET_CONTENTS_ON_LOGOUT', true);
}

/*
* if set to true, the article history of an user is saved in a cookie if not logged in
*/
if (!defined('SHOP_ALLOW_SAVING_ARTICLE_HISTORY_IN_COOKIE')) {
    define('SHOP_ALLOW_SAVING_ARTICLE_HISTORY_IN_COOKIE', false);
}

/**
 * if set to true external payment services should be run in live mode else paymentservice should run on test mode.
 * On some payment services like Ogone payment handler sends request to different payment urls.
 */
if (!defined('USE_LIVE_PAYMENT')) {
    /**
     * @deprecated This constant should no longer be in use. Use the environment config in the payment handler group config
     *              to switch between sandbox and production.
     */
    define('USE_LIVE_PAYMENT', false);
}

/**
 * some payment methods support a sandbox mode - usually this can be enabled/disabled via the config of the payment handler
 * use this constant to force enable sandbox mode even if it is disabled - this is useful if you want to test sandbox mode online
 * for a specific IP (ie the developer would like to test online).
 */
if (!defined('CMS_PAYMENT_USE_SANDBOX')) {
    /**
     * @deprecated This constant should no longer be in use. Use the environment config in the payment handler group config
     *              to switch between sandbox and production.
     */
    define('CMS_PAYMENT_USE_SANDBOX', false);
}

/**
 * If payment method return with failure on creating order do redirect to payment checkout page "system name = shipping".
 * If you don't want to redirect to payment checkout page enter empty value.
 * If your payment checkout page is in an other order step enter system name of the order step.
 */
if (!defined('CMS_PAYMENT_REDIRECT_ON_FAILURE')) {
    define('CMS_PAYMENT_REDIRECT_ON_FAILURE', 'shipping');
}

/*
* the max number of days that shop search log entries are kept.
* set to false if you don't want the log to be cleaned
*/
if (!defined('CHAMELEON_SHOP_SEARCH_LOG_MAX_AGE_IN_DAYS')) {
    define('CHAMELEON_SHOP_SEARCH_LOG_MAX_AGE_IN_DAYS', 182);
}

/*
* set to true if you want to anonymize saved ip for shop reviews
*/
if (!defined('CHAMELEON_PKG_SHOP_REVIEWS_ANONYMIZE_IP')) {
    define('CHAMELEON_PKG_SHOP_REVIEWS_ANONYMIZE_IP', true);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * PERFORMANCE
 * -------------------------------------------------------------------------------------------------------------------.
 */

/*
* use the load file for search index generation (much faster but requires file privileges for the db user
*/
if (!defined('CMS_SEARCH_INDEX_USE_LOAD_FILE')) {
    define('CMS_SEARCH_INDEX_USE_LOAD_FILE', false);
}

/*
 * if set to true, then the backend lists are cached in session
*/
if (!defined('CMS_ACTIVE_BACKEND_LIST_CACHE')) {
    /**
     * @deprecated since 6.2.0 - list caching will be removed in any later Chameleon version.
     */
    define('CMS_ACTIVE_BACKEND_LIST_CACHE', true);
}

/**
 * max number of logins to keep in history per user.
 * false = no limit.
 */
if (!defined('CHAMELEON_EXTRANET_LOGIN_HISTORY_LIMIT')) {
    define('CHAMELEON_EXTRANET_LOGIN_HISTORY_LIMIT', 50);
}

/**
 * if this is true, users must belong to a portal and are handled independently for each portal.
 */
if (!defined('CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT')) {
    define('CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT', false);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * PRIVACY
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * sets the max cookie lifetime in seconds for cookies other than the session cookie
 * default lifetime 14 days = 1209600 seconds
 * see also config constant: SHOP_ALLOW_SAVING_ARTICLE_HISTORY_IN_COOKIE.
 */
if (!defined('CHAMELEON_MAX_COOKIE_LIFETIME')) {
    define('CHAMELEON_MAX_COOKIE_LIFETIME', 1209600);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * OTHER
 * -------------------------------------------------------------------------------------------------------------------.
 */

/**
 * sets the PHP/Server timezone because sometimes this value is missing in PHP.INI.
 */
if (!defined('CMS_DEFAULT_TIME_ZONE')) {
    define('CMS_DEFAULT_TIME_ZONE', 'Europe/Berlin');
}

/*
 * enable flushing
*/
if (!defined('CHAMELEON_ENABLE_FLUSHING')) {
    define('CHAMELEON_ENABLE_FLUSHING', false);
}

/*
 * sets the timeout of record locks in minutes
*/
if (!defined('RECORD_LOCK_TIMEOUT')) {
    define('RECORD_LOCK_TIMEOUT', 1);
}

/**
 * Image-Magick options.
 */
if (!defined('IMAGEMAGICK_STRIP')) {
    define('IMAGEMAGICK_STRIP', true);
}
if (!defined('IMAGEMAGICK_IMG_QUALITY')) {
    define('IMAGEMAGICK_IMG_QUALITY', 90);
}

/**
 * GD-Lib options.
 */
if (!defined('JPEG_IMAGE_QUALITY')) {
    define('JPEG_IMAGE_QUALITY', 100);
}

/**
 * you may set ISO-8859-1 here or other encoding if you don`t send any non latin characters and got problems with older e-mail clients like Lotus Notes.
 */
if (!defined('CHAMELEON_EMAIL_ENCODING')) {
    define('CHAMELEON_EMAIL_ENCODING', 'UTF-8');
}

/**
 * defines the email print security level against spam bot indexing
 * chameleon will replace dots and the @ symbol in email addresses if level > 0.
 *
 * Level 0 -> off. emails will not be replaced.
 * Level 1 -> emails will be replaced by predefined strings, something like [Klammeraffe] [Punkt] (STANDARD)
 *            you can define your own replacement using $antiSpam->SetDefaultReplacementStrings()
 * Level 2 -> emails will be replaced by random strings
 */
if (!defined('CHAMELEON_EMAIL_PRINT_SECURITY_LEVEL')) {
    define('CHAMELEON_EMAIL_PRINT_SECURITY_LEVEL', 1);
}

/**
 * WYSIWYGPro line endings. possible values: DIV,BR,P
 * new default is "P".
 */
if (!defined('CHAMELEON_WYSIWYG_LINE_ENDINGS')) {
    define('CHAMELEON_WYSIWYG_LINE_ENDINGS', 'P');
}

/**
 * sets the buttons, that should be disabled by default for all WYSIWYGPro fields
 * see list of possible buttons in:
 * http://redmine.esono.de/attachments/5463/wysiwygPRO-Developers-Manual.pdf.
 */
if (!defined('CHAMELEON_WYSIWYG_DISABLED_BUTTONS')) {
    define('CHAMELEON_WYSIWYG_DISABLED_BUTTONS', '');
}

/**
 * if you manage more than one similar filename per document manager directory you would end up with wrong download links, because SEO URL is "/directoryID/filename.pdf" which has to be unique
 * activate this option to add the file id as suffix to the filename to make it unique.
 */
if (!defined('CHAMELEON_ENABLE_ID_SUFFIX_IN_DOWNLOAD_FILENAMES')) {
    define('CHAMELEON_ENABLE_ID_SUFFIX_IN_DOWNLOAD_FILENAMES', false);
}

/**
 * the max execution time for all cron jobs.
 */
if (!defined('CMS_MAX_EXECUTION_TIME_IN_SECONDS_FOR_CRONJOBS')) {
    define('CMS_MAX_EXECUTION_TIME_IN_SECONDS_FOR_CRONJOBS', 3600);
}

/**
 * when image magick is found it will be used for most image processing operations instead of the built
 * in gdlib. If you don't want this to happen you can disable ImageMagick here.
 */
if (!defined('DISABLE_IMAGEMAGICK')) {
    define('DISABLE_IMAGEMAGICK', false);
}

/**
 * allows you to disable session checks - use this if you want to access someones session.
 */
if (!defined('CHAMELEON_DEBUG_SESSION_DISABLE_SESSION_CHECK')) {
    define('CHAMELEON_DEBUG_SESSION_DISABLE_SESSION_CHECK', false);
}

/**
 * auto redirect to primary domain if a non primary domain is called.
 */
if (!defined('CHAMELEON_FORCE_PRIMARY_DOMAIN')) {
    define('CHAMELEON_FORCE_PRIMARY_DOMAIN', true);
}

if (!defined('CMS_SHOP_SEARCH_ENABLE_SPELLCHECKER')) {
    define('CMS_SHOP_SEARCH_ENABLE_SPELLCHECKER', true);
}

/**
 * you can define a prefix for all email subjects hier.
 */
if (!defined('CMS_PREFIX_ALL_MAIL_SUBJECTS')) {
    define('CMS_PREFIX_ALL_MAIL_SUBJECTS', false);
}

/**
 * path to the snippet tree relative to _CMS_CUSTOMER_CORE, _CMS_CUSTOM_CORE, and _CMS_CORE
 * the folder for the cms backend snippets are always in CMS_SNIPPET_PATH.'-cms'.
 */
if (!defined('CMS_SNIPPET_PATH')) {
    define('CMS_SNIPPET_PATH', 'snippets');
}

/**
 * enables the less parser for snippets (requires less compiler on the server).
 */
if (!defined('CMS_PKG_VIEW_RENDERER_ENABLE_LESS_COMPILER')) {
    define('CMS_PKG_VIEW_RENDERER_ENABLE_LESS_COMPILER', false);
}

/**
 * if set to true, then record will be possible to be locked by editor.
 */
if (!defined('CHAMELEON_ENABLE_RECORD_LOCK')) {
    define('CHAMELEON_ENABLE_RECORD_LOCK', true);
}

if (!defined('_DEVELOPMENT_DOMAIN')) {
    /**
     * @deprecated since 6.2.0 - use the service container to provide environment-dependent behavior.
     */
    define('_DEVELOPMENT_DOMAIN', '.intra');
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * pkgLikeCount module
 * -------------------------------------------------------------------------------------------------------------------.
 */
/**
 * default like count history age in days
 * set to false to keep complete history.
 */
if (!defined('CHAMELEON_PKG_LIKE_COUNT_HISTORY_MAX_AGE')) {
    define('CHAMELEON_PKG_LIKE_COUNT_HISTORY_MAX_AGE', 182);
}

/**
 * check if there are entries for record that do not exist anymore and delete them.
 */
if (!defined('CHAMELEON_PKG_LIKE_COUNT_CLEAN_ORPHANED_ENTRIES')) {
    define('CHAMELEON_PKG_LIKE_COUNT_CLEAN_ORPHANED_ENTRIES', true);
}

/**
 * -------------------------------------------------------------------------------------------------------------------
 * pkgNewsletter
 * -------------------------------------------------------------------------------------------------------------------.
 */
/**
 * Define if new pkgNewsletter module was used on page. If true, old newsletter module functions are not supported.
 * For Example old unsubscribe method with only an email given not longer supported.
 */
if (!defined('CHAMELEON_PKG_NEWSLETTER_NEW_MODULE')) {
    define('CHAMELEON_PKG_NEWSLETTER_NEW_MODULE', true);
}

/**
 * Path to the file that marks if maintenance mode is active.
 *
 * The following properties should be true for this location:
 * - It must survive deployment (if it was present before it should also be afterwards).
 * - Different Chameleon installations on the same host must be distinguishable here (by generating some kind of
 *   generated ID or using a path within the project directory).
 * - The same physical marker should be present for all nodes of a multi-node installation.
 *
 * The default value under "cmsdata" fulfills these requirements if cmsdata is shared between nodes in a multi-node
 * setup.
 */
if (!defined('PATH_MAINTENANCE_MODE_MARKER')) {
    define('PATH_MAINTENANCE_MODE_MARKER', PATH_CMS_CUSTOMER_DATA.'/maintenance/chameleon-maintenance-mode-marker');
}
