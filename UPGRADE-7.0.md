UPGRADE FROM 6.3 TO 7.0
=======================

# Removed Code Entities

The code entities in this list were marked as deprecated in previous releases and have now been removed.

## Services

## Container Parameters

## Bundle Configuration

## Constants

- _CONFIG_USE_WORKFLOW_ENGINE
- _CUSTOMER_SERVER_DBHOST
- _CUSTOMER_SERVER_DBNAME
- _CUSTOMER_SERVER_DBPWD
- _CUSTOMER_SERVER_DBUSER
- _DEVELOPMENT_DOMAIN
- _DEVELOPMENT_EMAIL
- _CONFIG_ALLOW_CACHING
- ACTIVE_BACKEND_TRANSLATION_API
- ALLOW_DATABASELOGGING
- AZURE_CLIENT_ID
- AZURE_CLIENT_SECRET
- CHAMELEON_PKG_EXTERNAL_TRACKER_DEMO_MODE
- CHAMELEON_PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CAMPAIGN_TRACKING
- CHAMELEON_CACHE_INCLUDE_CACHE_DELETE_TRACE_INFO
- CHAMELEON_CACHE_USE_FILE_SYSTEM_AS_STANDARD_CACHE
- CHAMELEON_DEBUG_LAST_ORDER
- CHAMELEON_DEBUG_PRINT_MODULE_RENDER_TIME
- CHAMELEON_LESS_CACHE_LESS_FILES
- CHAMELEON_MAX_CACHE_ITEM_LIVE_TIME_IN_SECONDS
- CHAMELEON_MEMCACHE_ACTIVATE
- CHAMELEON_MEMCACHE_CLASS
- CHAMELEON_MEMCACHE_MAX_CACHE_ITEM_LIVE_TIME_IN_SECONDS
- CHAMELEON_MEMCACHE_SERVER
- CHAMELEON_MEMCACHE_SERVER_PORT
- CHAMELEON_MEMCACHE_SERVER2
- CHAMELEON_MEMCACHE_SERVER_PORT2
- CHAMELEON_MEMCACHE_SESSIONS_SERVER
- CHAMELEON_MEMCACHE_SESSIONS_SERVER_2
- CHAMELEON_MEMCACHE_SESSIONS_SERVER_PORT
- CHAMELEON_MEMCACHE_SESSIONS_SERVER_PORT_2
- CHAMELEON_MEMCACHE_USE_FALLBACK
- CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE
- CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE
- CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR
- CHAMELEON_MEMCACHED_TIMEOUT_IN_MILLISECONDS
- CHAMELEON_SEO_URL_REWRITE_TO_CLEAN
- chameleon::REQUEST_TYPE_ASSETS
- chameleon::REQUEST_TYPE_BACKEND
- chameleon::REQUEST_TYPE_BOOT_ONLY
- chameleon::REQUEST_TYPE_FRONTEND
- chameleon::REQUEST_TYPE_UNITTEST
- ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface::REQUEST_TYPE_BOOT_ONLY
- ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface::REQUEST_TYPE_UNITTEST
- CMS_ACTIVE_REVISION_MANAGEMENT
- CMS_ACTIVE_SMART_URL_HANDLER_CACHING
- CMS_BOT_DISABLE_SESSION_LOCKING
- CMS_COOKIE_HTTP_ONLY
- CMS_CORE_CACHE_DEFAULT_MAX_AGE_IN_SECONDS
- CMS_DEBUG_CACHE_KEYS
- CMS_DEBUG_CACHE_RECORD
- CMS_DEBUG_CACHE_RECORDLIST
- CMS_ENABLE_FIREPHP
- CMS_INSTALL_FOLDER_CHECK
- CMS_LOG_LEVEL
- CMS_MAX_SESSION_LIFETIME
- CMS_OUTPUT_PAGE_LOAD_TIME_INFO
- CMS_PAYMENT_USE_SANDBOX
- CMS_SESSION_KEEP_ACTIVE_ON_BROWER_CLOSE
- CMS_SESSION_USE_MEMCACHE_NATIVE_DRIVER
- CMS_SHOW_TASKS
- CMS_SHOW_TASKS_INFORMATION
- CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE_IN_LISTMANAGER
- CMS_URL_AUTO_REWRITE_UNDERSCORE
- CREATE_PAGE_REVISION_ON_WORKFLOW_PUBLISH
- DEBUG_SHOW_VIEW_SOURCE_HTML_HINTS
- ENABLE_EXTERNAL_RESOURCE_COLLECTION
- ENABLE_EXTERNAL_RESOURCE_COLLECTION_MINIFY
- ENABLE_EXTERNAL_RESOURCE_COLLECTION_REFRESH_PREFIX
- MTHeader::CONFIGPARAM_DB_COUNTER
- MTHeader::CONFIGPARAM_TIMESTAMP
- MTHeader::DB_LOGGING_STATE
- MTHeader::TIMESTAMP_CREATED_IN_SESSION
- PATH_CMS_CUSTOMER_WORKFLOW_DOCUMENT
- PATH_CMS_CUSTOMER_WORKFLOW_MEDIA
- PATH_CMS_CUSTOMER_WORKFLOW_MEDIA_THUMBS
- PATH_CMS_FONTS
- PATH_CMS_UPDATE
- PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CROSS_DOMAIN_TRACKING
- REQUEST_TRUSTED_PROXIES
- REQUEST_TRUSTED_HEADER_CLIENT_HOST
- REQUEST_TRUSTED_HEADER_CLIENT_IP
- REQUEST_TRUSTED_HEADER_CLIENT_PORT
- REQUEST_TRUSTED_HEADER_CLIENT_PROTO
- TCMSCronJob::LOG_FILE
- TCMSMemcache::CACHE_DRIVER_MEMCACHE
- TCMSMemcache::CACHE_DRIVER_MEMCACHED
- TCMSUserInput::FILTER_EMAIL
- TCMSUserInput::FILTER_XSS
- TDataExtranetUser::MAX_SALT_AGE_IN_SECONDS
- USE_FILE_BASED_SESSION_HANDLING
- USE_IMAGELAYER
- USE_LIGHTBOX
- USE_LIVE_PAYMENT
- USE_ONLY_COOKIES_FOR_SESSION_ID

## Classes and Interfaces

- AbstractPkgCmsProfilerItem
- CMSMediaViddlerImport
- IClusterDriver
- ICmsObjectLink
- IMapperCacheManager
- IMapperCacheManagerRestricted
- IPkgCmsEventObservable
- IPkgCmsServerSetupValidator
- IPkgCmsServerSetupValidatorMessage
- IPkgCoreDbClassFactory
- IUserCustomModelBase
- MapperCacheManager
- MapperCacheManagerException
- MapperCacheManagerExceptionContentNotFound
- MapperCacheManagerRestrictedProxy
- MTPkgExternalTrackerGoogleAnalytics_MTPageMetaCore
- TCacheManagerStorage_Decorator
- TCacheManagerStorage_Decorator_LazyWriteMemcache
- TCacheManagerStorage_Standard
- TCMSDataExtranetUser
- TCMSFontImage
- TCMSFontImageList
- TCMSMath
- TCmsObjectLinkBase
- TCmsObjectLinkException_InvalidTargetClass
- TCMSSmartURLHandler_Pagepath
- TCMSUserInput_EMail
- TCMSUserInput_XSS
- TPkgCmsClassManager_CmsConfig
- TPkgCmsCore
- TPkgCmsFileManagerException
- TPkgCmsProfileItem_Group
- TPkgCmsProfileItem_Tick
- TPkgCmsProfiler
- TPkgCmsServerSetupValidator_PHPVersion
- TPkgCmsServerSetupValidatorManager
- TPkgCmsServerSetupValidatorMessage
- TPkgCmsSessionHandler_Decorator_Observable
- TPkgExternalTrackerGoogleAnalytics

## Properties

- ChameleonSystem\CoreBundle\Controller\ChameleonController::$portalDomainService
- ChameleonSystem\CoreBundle\Controller\ChameleonController::$redirectPageDef
- ChameleonSystem\CoreBundle\Controller\ChameleonController::$requestInfoService
- MTPageMetaCoreEndPoint::$oActivePortal
- TCMSMemcache::$sUsedMemcacheClass
- TGlobal::$oURLHistory
- TGlobalBase::$aUnitTestMockedObjects
- TModelBase::$isExportCall

## Methods

- ChameleonSystem\CoreBundle\Controller\ChameleonController::GetExecutionTime()
- ChameleonSystem\CoreBundle\Controller\ChameleonController::postRoutingHook()
- ChameleonSystem\CoreBundle\Controller\ChameleonController::setOutputPageLoadTimeInfo()
- ChameleonSystem\CoreBundle\Service\Initializer\RequestInitializer::handleUnitTestCase()
- ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getPageDataForPagePath()
- ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getRouteForTree()
- esono\pkgCmsCache\Cache::setCacheDir()
- IPkgCmsFileManager::setDriver()
- MTHeader::_LoadUserImage()
- MTHeader::AddCounter
- MTHeader::ChangeActiveDbCounter
- MTHeader::FetchCounterInformation
- MTPageMetaCoreEndPoint::GetETrackerId()
- MTPageMetaCoreEndPoint::IncludeTrackerEtracker()
- MTPageMetaCoreEndPoint::IncludeTrackerEtrackerHook()
- MTPageMetaCoreEndPoint::IncludeTrackerGoogleAnalytics()
- TAdb*List::GetDefaultLanguageId()
- TCMSActivePage::GetActiveLanguage()
- TCMSActivePage::GetActiveLanguageObject()
- TCMSActivePage::GetInstanceWithoutCache()
- TCMSConfig::ExtranetModuleInstalled()
- TCMSConfig::GetGlobalCacheKeyParameter()
- TCMSConfig::ObjectIsInitialized()
- TCMSContentBoxItem::_loadMenuItems()
- TCmsLanguage::getLanguageFromIsoCodeCached()
- TCMSLogChange::_WriteTransactionFooter()
- TCMSLogChange::_WriteTransactionHeader()
- TCMSLogChange::addShopSystemPage()
- TCMSLogChange::DisablePHPCommentsInDbLog()
- TCMSLogChange::EndTransaction()
- TCMSLogChange::getActiveDbCounterName()
- TCMSLogChange::setActiveDbCounterName()
- TCMSLogChange::UpdateCounterExists()
- TCMSMail::TransformEmailInDevelopmentMode()
- TCMSMemcache::getDriverType()
- TCMSPage::GetMainTree()
- TCMSPortal::GetPortalBaseURL()
- TCMSPortal::GetPortalHomeURL()
- TCMSPortal::GetPrimaryDomainObject()
- TCMSPortal::GetPrimarySSLDomain()
- TCMSPortal::GetSSLDomainForCurrentURL()
- TCMSPortal::getRootURL()
- TCMSPortalDomain::GetActiveDomain()
- TCMSPortalDomain::IsDevelopmentDomain()
- TCMSRecord::IsMultiLanguageTable()
- TCMSTableEditorMedia::RefreshImageOnViddler()
- TCMSTableToClass::getDatabaseConnection()
- TCMSTableToClass::setDatabaseConnection()
- TCMSTextFieldEndPoint::_RemoveProprietaryParameter()
- TCMSTreeNode::ConvertToValidXHTMLLink()
- TCMSUpdateManager::checkIfUpdateHasBeenProcessed()
- TCMSUpdateManager::getBuildNumbersForFolder()
- TCMSUpdateManager::getLatestBuildNumberForFolder()
- TCMSUpdateManager::RunCoreUpdates()
- TCMSUser::generateHash()
- TCMSUserInput::AutoAddAuthenticityTokenToForms()
- TCMSUserInput::CallbackAddAuthenticityTokenToForms()
- TCMSUserInput::CheckAuthenticityTokenInInput()
- TCMSUserInput::GenerateNewAuthenticityToken()
- TCMSUserInput::GetAuthenticityToken()
- TCMSUserInput::GetAuthenticityTokenName()
- TCMSUserInput::GetAuthenticityTokenString()
- TCMSUserInput::HasActiveAutoProtectInputViaAuthenticityToken()
- TDataExtranetCore::CacheCommit()
- TDataExtranetCore::IsExtranetUsingCryptedPassword()
- TDataExtranetUser::CommitToSession()
- TDataExtranetUser::GetLinkSendDoubleOptInEmail()
- TDataExtranetUser::GetPasswordSalt()
- TDataExtranetUser::getRedirectToAccessDeniedPageLink()
- TDataExtranetUser::RedirectToAccessDeniedPage()
- TGlobal::GetActiveLanguageId()
- TGlobalBase::_GetModuleRootPath()
- TGlobalBase::AddFileToPHPFileCache()
- TGlobalBase::ClassFactory()
- TGlobalBase::DeleteUnitTestMockedObject()
- TGlobalBase::FieldExists()
- TGlobalBase::GetActiveLanguageId()
- TGlobalBase::GetActiveLanguagePrefix()
- TGlobalBase::GetMemcacheInstance()
- TGlobalBase::GetPathWebLibrary()
- TGlobalBase::GetUnitTestMockedObject()
- TGlobalBase::IsCMSTemplateEngineEditMode()
- TGlobalBase::LoadDBObjectClassDefinition()
- TGlobalBase::LoadClassDefinition()
- TGlobalBase::NewDBObject()
- TGlobalBase::OutputDataAsFormFields()
- TGlobalBase::OutputDataAsURL()
- TGlobalBase::RegisterUnitTestMockedObject()
- TGlobalBase::ReplaceCustomVariablesInString()
- TGlobalBase::WriteLog()
- TModelBase::AllowPageCache()
- TModelBase::ClearCache()
- TModelBase::ExecuteExport()
- TModelBase::GetExportLink()
- TModelBase::GetExportView()
- TPkgCmsRouteControllerCmsTplPage::isNonSeoLink()
- TPkgComment::GetDefaultLanguageId()
- TPkgCommentModuleConfig::GetDefaultLanguageId()
- TTools::ConvertMySQL2UnixTimeStamp()
- TTools::DateTime2UnixTimestamp()
- TTools::GenerateEncryptedPassword()
- TTools::GetLanguageISOName()
- TTools::GetPageObject()
- TTools::RealNameToURLName()
- TTools::sanitize_filename()
- TTools::sendToHost()
- TTools::sendToHostReturnFull()
- TTools::UnicodeToEntitiesPreservingAscii()
- TTools::UTF8ToEntitiesPreservingAscii()
- TTools::UTF8ToUnicode()

## JavaScript Files and Functions

- bootstrap-colorpicker (new version 3.0.3 located in src/CoreBundle/Resources/public/javascript/jquery/bootstrap-colorpicker-3.0.3).
- chosen.jquery.js
- html5shiv.js
- jqModal.js 
- jqDnR.js
- jquery.form.js (new version 4.2.2 located in src/CoreBundle/Resources/public/javascript/jquery/jquery-form-4.2.2/jquery.form.min.js).
- jquery.selectboxes.js
- jQueryUI (everything in path src/CoreBundle/Resources/public/javascript/jquery/jQueryUI; drag and drop still used in the template engine).
- maskedinput.js
- pngForIE.htc
- pNotify (new version 3.2.0 located in src/CoreBundle/Resources/public/javascript/pnotify-3.2.0/)
- respond.min.js
- rwd.images.js
- src/CoreBundle/Resources/public/javascript/mainNav.js
- TDataCustomListConfig.js
- THTMLFileBrowser.js
- THTMLTable.js

- $.addOption() (jquery.selectboxes plugin)
- $.bgiframe()
- $.blockUI()
- $.everyTime()
- $.jBreadCrumb()
- $.jqDnR() (part of jqModal)
- $.jqM() (jqModal)
- $.oneTime()
- $.stopTime()
- $.tagInput()
- $.unblockUI()
- $.wTooltip()
- ActivateRecordRevision()
- AddNewRevision()
- CreateModalIFrameDialogFromContentWithoutClose()
- PublishViaAjaxCallback()
- SaveNewRevision()
- SetChangedDataMessage()
- showMLTField()
- SwitchEditPortal()
- SwitchEditPortalCallback()

## Translations

## Database Tables

## Database Fields

## Icons

All icons in CoreBundle/Resources/public/images/ 
and CoreBundle/Resources/public/themes/standard/images/ (not recursively, subfolders remain) were deleted. 
Exceptions are the chameleon logos and favicon.ico.

The Font Awesome icon font was upgraded to 5.8.1.

## Frontend Assets

There were some frontend styles, images and javascript helpers located in the core that were deleted or moved to bundles.

- web_modules/MTConfigurableFeedbackCore
- web_modules/MTExtranet
- web_modules/MTFAQListCore
- web_modules/MTFeedbackCore
- web_modules/MTGlobalListCore
- web_modules/MTNewsletterSignupCore
