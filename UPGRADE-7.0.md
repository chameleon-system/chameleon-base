UPGRADE FROM 6.3 TO 7.0
=======================

# Essentials

The steps in this chapter are required to get the project up and running in version 7.0.
It is recommended to follow these steps in the given order.

## Prepare Project

Be sure to install the latest release of the Chameleon 6.3.x branch before migrating. It is recommended to remove all
deprecated code usage from the project before migrating. This way there will be deprecation messages still available
which will help to decide what to do with code calling deprecated entities.

Note that we decided to keep some deprecated entities although this is a major release. See section
`Still-deprecated Code Entities` for details. These entities will be removed in a future Chameleon release, so be sure
to remove calls over time.

Logout from the Chameleon backend.

## Adjust Composer Dependencies

In `composer.json`, adjust version constraints for all Chameleon dependencies from `~6.3.0` to `~7.0.0` and run
`composer update`.

## Regenerate Autoclasses And Run Updates

Regenerate autoclasses, either by deleting the `app/cache/autoclasses` directory and calling the Chameleon backend, or
by calling the console command `app/console chameleon_system:autoclasses:generate`.

Login to the Chameleon backend as administrator then and run updates.

## Pagedef No Longer In Request Data

When the user sends a request, the system in general first tries to find which page to load. If a page is found in this
routing process, the ID of that page, called pagedef, is saved in the request attributes. In previous Chameleon releases
this pagedef was additionally saved as request query parameter, which is no longer the case.

Your project code should no longer retrieve the pagedef from the request query parameters, nor set it (e.g. in older
SmartURLHandlers). An exception to this rule is in code that is only ever executed in backend context, where it is
still valid.

The following examples show what is NO LONGER working for code that may run in frontend context in Chameleon 7.0: 

```php
$request = ServiceLocator::get('request_stack'')->getCurrentRequest();
$request->query->get('pagedef');
```

```php
$inputFilterUtil = ServiceLocator::get('chameleon_system_core.util.input_filter');
$inputFilterUtil->getFilteredGetInput('pagedef');
```

The following examples show what still works and does not need changes. Note that only the first example is considered
the "correct" way. The second example uses a generic method that doesn't distinguish between GET, POST and request
attributes which is considered a bad practice. The third example uses deprecated methods in TGlobal that do still work,
but will be removed in a future Chameleon release.

```php
$request = ServiceLocator::get('request_stack'')->getCurrentRequest();
$request->attributes->get('pagedef');
$request->get('pagedef');
```

```php
$inputFilterUtil = ServiceLocator::get('chameleon_system_core.util.input_filter');
$inputFilterUtil->getFilteredInput('pagedef');
```

```php
$global = TGlobal::instance();
$global->GetUserData('pagedef');

```

# Cleanup

## Remove Flash Files

Support for Adobe Flash was removed. We recommend to search the media manager for legacy Flash files (search for file
extensions "flv", "f4v" and "swf") and remove them.
The media manager will also display where these files are still used; these usages should also be removed.

## Remove module_cms_search Table

The CMS search module was removed. If the project still needs this module, restore it from an earlier Chameleon release.
Otherwise remove the associated database table by calling `TCMSLogChange::deleteTable('module_cms_search')` in an update
script.

# Informational

## JavaScript disallowed in WYSIWYG fields by default

JavaScript in WYSIWYG fields poses a potential security risk which is why it is now disallowed by default. If the
project requires WYSIWYG JavaScript, allow it by setting the configuration key
`chameleon_system_cms_text_field: allow_script_tags: true`.

# Changed Features

## Changed Interfaces and Method Signatures

### TCMSTableEditorEndPoint

- Removed argument 1 from method `DeleteRecordReferencesFromSource`.
- Removed argument 2 from method `DeleteRecordReferencesProperties`.

### TCMSTableEditorModuleInstance

- Removed argument 1 from method `DeleteRecordReferenceModuleContent`.
- Removed argument 3 from method `GetConnectedTableRecords`.

# Removed Features

## RevisionManagementBundle

The RevisionManagementBundle was removed. Remove it from the AppKernel.

# Deprecated Code Entities

There is a small number of entities newly deprecated since 7.0.0.

## Database Fields

- cms_tbl_conf.icon_list
- cms_tpl_module.icon_list

# Still-deprecated Code Entities

Some code entities have been marked as deprecated in previous releases, but were not removed in this release because of
wide usage, bug risks or quite a short deprecation period. They will be removed in a future Chameleon release, so be
sure to remove calls over time.

Entities that were NOT removed include (incomplete list):

- Any database fields
- Almost any method arguments
- Classic main menu
- Anything logging-related
- TGlobal::GetUserData() and related methods
- TGlobal::Translate()
- TTools::GetArrayAsURL() and TTools::GetArrayAsURLForJavascript()
- TCMSLogChange::_RunQuery()
- MySQLLegacySupport
- instance() methods

# Removed Code Entities

The code entities in this list were marked as deprecated in previous releases and have now been removed.

## Services

- chameleon_system_core.ChameleonSessionManager
- chameleon_system_core.password
- chameleon_system_core.util.snippet_chain

## Container Parameters

- chameleon_system_core.allow_database_logging
- chameleon_system_core.cache.memcache_class
- chameleon_system_core.cms_log_level
- chameleon_system_core.debug.cms_debug_cache_record
- chameleon_system_core.debug.cms_debug_cache_recordlist
- chameleon_system_core.debug.cms_output_page_load_time_info
- chameleon_system_core.debug.print_module_render_time
- chameleon_system_core.render_media_properties_field

## Bundle Configuration

None.

## Constants

- _CMS_CORE_ENGINE
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
- DEBUG_LEVEL
- DEBUG_SHOW_VIEW_SOURCE_HTML_HINTS
- ENABLE_EXTERNAL_RESOURCE_COLLECTION
- ENABLE_EXTERNAL_RESOURCE_COLLECTION_MINIFY
- ENABLE_EXTERNAL_RESOURCE_COLLECTION_REFRESH_PREFIX
- HTTP_PATH_ROOT
- MTHeader::CONFIGPARAM_DB_COUNTER
- MTHeader::CONFIGPARAM_TIMESTAMP
- MTHeader::DB_LOGGING_STATE
- MTHeader::TIMESTAMP_CREATED_IN_SESSION
- PATH_CMS_CUSTOMER_WORKFLOW_DOCUMENT
- PATH_CMS_CUSTOMER_WORKFLOW_MEDIA
- PATH_CMS_CUSTOMER_WORKFLOW_MEDIA_THUMBS
- PATH_CMS_FONTS
- PATH_CMS_UPDATE
- PATH_FILETYPE_ICONS
- PATH_FILETYPE_ICONS_LOW_QUALITY
- PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CROSS_DOMAIN_TRACKING
- REQUEST_TRUSTED_PROXIES
- REQUEST_TRUSTED_HEADER_CLIENT_HOST
- REQUEST_TRUSTED_HEADER_CLIENT_IP
- REQUEST_TRUSTED_HEADER_CLIENT_PORT
- REQUEST_TRUSTED_HEADER_CLIENT_PROTO
- TCMSCronJob::LOG_FILE
- TCMSMemcache::CACHE_DRIVER_MEMCACHE
- TCMSMemcache::CACHE_DRIVER_MEMCACHED
- TCMSTableEditorEndPoint::DELETE_REFERENCES_REVISION_DATA_WHITELIST_SESSION_VAR
- TCMSUserInput::FILTER_EMAIL
- TCMSUserInput::FILTER_XSS
- TDataExtranetUser::MAX_SALT_AGE_IN_SECONDS
- URL_FILETYPE_ICONS
- URL_FILETYPE_ICONS_LOW_QUALITY
- USE_FILE_BASED_SESSION_HANDLING
- USE_IMAGELAYER
- USE_LIGHTBOX
- USE_LIVE_PAYMENT
- USE_ONLY_COOKIES_FOR_SESSION_ID

## Classes and Interfaces

- _TCMSMediaTreeNodeMediaObj
- AbstractPkgCmsProfilerItem
- ChameleonSystem\core\DatabaseAccessLayer\Workflow\WorkflowQueryModifierOrderBy
- ChameleonSystem\CoreBundle\Service\MediaManagerUrlGenerator
- ChameleonSystem\CoreBundle\Util\SnippetChainUtil
- ChameleonSystem\RevisionManagementBundle\ChameleonSystemRevisionManagementBundle
- CMSFieldMLT
- CMSMediaLocalImport
- CMSMediaManager
- CMSMediaManagerTreeRPC
- CMSMediaViddlerImport
- CMSModuleImagePool
- CMSSearch
- esono\pkgCmsRouting\exceptions\DomainNotFoundException
- IClusterDriver
- ICmsObjectLink
- IMapperCacheManager
- IMapperCacheManagerRestricted
- IPkgCmsEventObservable
- IPkgCmsSecurity_Password
- IPkgCmsServerSetupValidator
- IPkgCmsServerSetupValidatorMessage
- IPkgCoreDbClassFactory
- IUserCustomModelBase
- MapperCacheManager
- MapperCacheManagerException
- MapperCacheManagerExceptionContentNotFound
- MapperCacheManagerRestrictedProxy
- MTGoogleMyMapsCore
- MTListCore
- MTPassThrough
- MTPkgExternalTrackerGoogleAnalytics_MTPageMetaCore
- MTSearchCore
- MTSendAFriendCore
- MTSitemapCore
- TCacheManagerStorage_Decorator
- TCacheManagerStorage_Decorator_LazyWriteMemcache
- TCacheManagerStorage_Standard
- TCMSCronJob_CreateSearchIndex
- TCMSDataExtranetUser
- TCMSFieldMediaProperties
- TCMSFieldWorkflowActionType
- TCMSFieldWorkflowAffectedRecord
- TCMSFieldWorkflowBool
- TCMSFieldWorkflowPublishActive
- TCMSFontImage
- TCMSFontImageList
- TCMSListManagerRevisionManagement
- TCMSMath
- TCMSMediaConnections
- TCMSMediaTreeNode
- TCmsObjectLinkBase
- TCmsObjectLinkException_InvalidTargetClass
- TCMSSearchIndex
- TCMSSearchIndexPage
- TCMSSearchIndexPortal
- TCMSSessionHandler
- TCMSSmartURLHandler_EOSNeoPay
- TCMSSmartURLHandler_FlashCrossDomain
- TCMSSmartURLHandler_Pagepath
- TCMSTableEditorRecordRevision
- TCMSUserInput_EMail
- TCMSUserInput_XSS
- THTMLFileBrowser
- TPkgCmsClassManager_CmsConfig
- TPkgCmsCore
- TPkgCmsFileManagerException
- TPkgCmsLicenseManager_MTLogin
- TPkgCmsProfileItem_Group
- TPkgCmsProfileItem_Tick
- TPkgCmsProfiler
- TPkgCmsSecurity_Password
- TPkgCmsServerSetupValidator_PHPVersion
- TPkgCmsServerSetupValidatorManager
- TPkgCmsServerSetupValidatorMessage
- TPkgCmsSessionHandler_Decorator_Observable
- TPkgExternalTrackerGoogleAnalytics
- TPkgShopPaymentEOS_TPkgShopStoredUserPaymentMapper_CreditCard
- TPkgSnippetRenderer_TranslationNode
- TPkgSnippetRenderer_TranslationTokenParser
- TPkgViewRenderer_TCMSSmartURLHandler_SnippetLessCompiler
- TShopPaymentHandler_EOSNeoPay
- TShopPaymentHandler_EOSNeoPayCreditCard

## Properties

- antiSpam::$emailIcon
- ChameleonSystem\CoreBundle\Controller\ChameleonController::$portalDomainService
- ChameleonSystem\CoreBundle\Controller\ChameleonController::$redirectPageDef
- ChameleonSystem\CoreBundle\Controller\ChameleonController::$requestInfoService
- CMSModuleChooser::$bModuleInstanceIsLockedByWorkflowTransaction
- CMSModuleChooser::$bPageIsLockedByWorkflowTransaction
- MTPageMetaCoreEndPoint::$oActivePortal
- TAccessManagerPermissions::$revisionManagement
- TAccessManagerPermissions::$workflowPublish
- TCMSFieldLookupFieldTypes::$sFieldHelpTextHTML
- TCMSFile::$sTypeIcon
- TCMSImageEndpoint::$bAutoPlay
- TCMSImageEndpoint::$bFlashVideoZoomPopup
- TCMSImageEndpoint::$FLVPlayerHeight
- TCMSImageEndpoint::$FLVPlayerURL
- TCMSImageEndpoint::$iShowImageFromWorkflow
- TCMSImageEndpoint::$sFlashPlayerSkinURL
- TCMSImageEndpoint::$sFlashVideoWMode
- TCMSMemcache::$sUsedMemcacheClass
- TCMSRecord::$bBypassWorkflow
- TCMSRecord::$bDataLoadedFromWorkflow
- TCMSRecordList::$bForceWorkflow
- TCMSRecordList::$bUseGlobalFilterInsteadOfPreviewFilter
- TCMSRecordList::$sTableObjectSubtype
- TCMSRecordList::$sTableObjectType
- TCMSTableEditorEndPoint::$bBypassWorkflow
- TCMSTableEditorEndPoint::$bWorkflowActive
- TCMSTableEditorEndPoint::$bWorkflowIsUpdateFollowingAnInsert
- TCMSTableEditorChangeLog::$oOldFields
- TCMSTableEditorEndPoint::$aErrors
- TCMSTableEditorMedia::$oFLVMetaData
- TCMSUser::$bWorkflowEngineActive
- TCMSWizardStep::$bHasMethodExecutionCalled
- TFullGroupTable::$iconSortASC
- TFullGroupTable::$iconSortDESC
- TGlobal::$oURLHistory
- TGlobalBase::$aUnitTestMockedObjects
- THTMLTable::$aCachTriggerTables
- TModelBase::$isExportCall
- TViewParser::$aCacheClearTriggers
- TViewParser::$aCacheParameters
- TViewParser::$bUseCaching

## Methods

- ChameleonSystem\CoreBundle\Controller\ChameleonController::GetExecutionTime()
- ChameleonSystem\CoreBundle\Controller\ChameleonController::postRoutingHook()
- ChameleonSystem\CoreBundle\Controller\ChameleonController::setOutputPageLoadTimeInfo()
- ChameleonSystem\CoreBundle\CronJob\CronJobFactory::setCronJobs()
- ChameleonSystem\CoreBundle\Interfaces\TransformOutgoingMailTargetsServiceInterface::setEnableTransformation()
- ChameleonSystem\CoreBundle\Interfaces\TransformOutgoingMailTargetsServiceInterface::setSubjectPrefix()
- ChameleonSystem\CoreBundle\ModuleService\ModuleResolver::addModule()
- ChameleonSystem\CoreBundle\ModuleService\ModuleResolver::addModules()
- ChameleonSystem\CoreBundle\ModuleService\ModuleResolver::getModules()
- ChameleonSystem\CoreBundle\Service\Initializer\RequestInitializer::handleUnitTestCase()
- ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel::isBIgnoreWorkflow
- ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel::setBIgnoreWorkflow
- ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getPageDataForPagePath()
- ChameleonSystem\CoreBundle\Util\RoutingUtilInterface::getRouteForTree()
- CMSModuleChooser::LoadWorkflowData()
- CMSModuleImageManager::HandleWorkflowOnSetImage()
- CMSModulePageTree::GetTransactionDetails()
- CMSTableExport::GenerateHTMLExport()
- CMSTableExport::getCMSCustomerStyles()
- CMSTemplateEngine::GetLastRevisionNumber()
- CMSTemplateEngine::GetMainNavigation()
- CMSTemplateEngine::LoadRevisionData()
- CMSTemplateEngine::LoadWorkflowData()
- esono\pkgCmsCache\Cache::setCacheDir()
- esono\pkgCmsCache\CacheInterface::getRequestStateKey()
- gcf_workflowEditedRecordname()
- gcf_workflowLastChange()
- gcf_workflowStatus()
- IPkgCmsFileManager::setDriver()
- MTExtranetCoreEndPoint::PostRegistrationHook()
- MTHeader::_LoadUserImage()
- MTHeader::AddCounter()
- MTHeader::ChangeActiveDbCounter()
- MTHeader::FetchCounterInformation()
- MTHeader::GetCurrentTransactionInfo()
- MTPageMetaCoreEndPoint::GetETrackerId()
- MTPageMetaCoreEndPoint::IncludeTrackerEtracker()
- MTPageMetaCoreEndPoint::IncludeTrackerEtrackerHook()
- MTPageMetaCoreEndPoint::IncludeTrackerGoogleAnalytics()
- MTTableEditor::ActivateRevision()
- MTTableEditor::AddNewRevision()
- MTTableEditor::GetLastRevisionNumber()
- MTTableEditor::LoadRevisionData()
- MTTableEditor::LoadWorkflowData()
- MTTableEditor::TranslateString()
- MTTableEditor::PublishViaAjax()
- TAccessManager::HasRevisionManagementPermission()
- TAccessManager::HasWorkflowEditPermission()
- TAccessManager::HasWorkflowPublishPermission()
- TAccessManager::InitFromDatabase()
- TAccessManagerGroups::GetGroupName()
- TAccessManagerPermissions::GetRevisionManagementPermissionStatus()
- TAccessManagerPermissions::GetWorkflowPublishStatus()
- TAccessManagerUser::InitFromDatabase()
- TAdb*List::EditedRecordsAvailable()
- TAdb*List::GetDefaultLanguageId()
- TAdb*List::IsTableWithActiveWorkflow()
- TCMSActivePage::GetActiveLanguage()
- TCMSActivePage::GetActiveLanguageObject()
- TCMSActivePage::GetInstanceWithoutCache()
- TCMSConfig::ExtranetModuleInstalled()
- TCMSConfig::GetGlobalCacheKeyParameter()
- TCMSConfig::ObjectIsInitialized()
- TCMSContentBoxItem::_loadMenuItems()
- TCMSDownloadFileEndPoint::GetDownloadLink()
- TCMSField::RenderInputFrontend()
- TCMSFieldColorpicker::isFirstInstance()
- TCMSFieldTreeNode::GetPageTreeConnectionDateInformationHTML()
- TCMSImageEndpoint::GetFlashPlugin()
- TCMSImageEndpoint::GetFlashVarsArray()
- TCMSImageEndpoint::GetPlayerUrl()
- TCMSImageEndpoint::GetThumbnailTag()
- TCMSImageEndpoint::IsFlashMovie()
- TCMSImageEndpoint::renderFLV()
- TCMSImageEndpoint::renderSWF()
- TCmsLanguage::getLanguageFromIsoCodeCached()
- TCMSListManagerEndPoint::GetWorkflowRestrictions()
- TCMSListManagerFullGroupTable::CallBackWorkflowActionType()
- TCMSListManagerFullGroupTable::IsCmsWorkflowTransaction()
- TCMSListManagerMLT::CallBackWorkflowConnectionActionType()
- TCMSLogChange::_SetFieldPosition()
- TCMSLogChange::_WriteTransactionFooter()
- TCMSLogChange::_WriteTransactionHeader()
- TCMSLogChange::DisablePHPCommentsInDbLog()
- TCMSLogChange::EndTransaction()
- TCMSLogChange::getActiveDbCounterName()
- TCMSLogChange::GetCmsContentBoxIdFromName()
- TCMSLogChange::getUpdateLogger()
- TCMSLogChange::setActiveDbCounterName()
- TCMSLogChange::UpdateCounterExists()
- TCMSMail::TransformEmailInDevelopmentMode()
- TCMSMemcache::getDriverType()
- TCMSMessageManager::SaveToSession()
- TCMSTableEditorChangeLog::savePreSaveValues()
- TCMSPage::GetMainTree()
- TCMSPortal::getActivePortalLessSuffix()
- TCMSPortal::GetPortalBaseURL()
- TCMSPortal::GetPortalHomeURL()
- TCMSPortal::GetPrimaryDomainObject()
- TCMSPortal::GetPrimarySSLDomain()
- TCMSPortal::GetSSLDomainForCurrentURL()
- TCMSPortal::getRootURL()
- TCMSPortalDomain::GetActiveDomain()
- TCMSPortalDomain::IsDevelopmentDomain()
- TCMSRecord::GetMLTTargetTableNameFromMLTField()
- TCMSRecord::GetWorkflowMLTFilterQuery()
- TCMSRecord::GetWorkflowRestrictionQuery()
- TCMSRecord::IsMultiLanguageTable()
- TCMSRecord::IsTableWithWorkflow()
- TCMSRecord::PostLoadHookFromUnitTest()
- TCMSRecord::SetWorkflowByPass()
- TCMSSmartURL::GetDirectURL()
- TCMSSmartURL::GetDocumentNotFoundPagedef()
- TCMSSmartURL::GetLanguagePrefixForPortal()
- TCMSSmartURL::GetNotFoundPagedef()
- TCMSSmartURL::GetPagePathForTreeId()
- TCMSSmartURL::GetPathDomainPrefix()
- TCMSSmartURL::GetURL()
- TCMSSmartURL::GetURLFast()
- TCMSSmartURL::RealNameToURLName()
- TCMSSmartURL::SetRealPagdef()
- TCMSTableEditorDocumentEndPoint::GetMltReferencesRecordList()
- TCMSTableEditorDocumentEndPoint::GetRecordsWithWysiwygDownload()
- TCMSTableEditorDocumentEndPoint::MoveWorkflowDocumentToDocumentPool()
- TCMSTableEditorEndPoint::ActivateMLTRecordRevisions()
- TCMSTableEditorEndPoint::ActivateRecordRevision()
- TCMSTableEditorEndPoint::ActivateRecordRevision_Execute()
- TCMSTableEditorEndPoint::AddInsertWorkflowAction()
- TCMSTableEditorEndPoint::AddNewRevision()
- TCMSTableEditorEndPoint::AddNewRevision_Execute()
- TCMSTableEditorEndPoint::AddNewRevisionForConnectedPropertyRecords()
- TCMSTableEditorEndPoint::AddNewRevisionForMLTConnectedRecords()
- TCMSTableEditorEndPoint::AddNewRevisionForSingleFields()
- TCMSTableEditorEndPoint::AddNewRevisionFromDatabase()
- TCMSTableEditorEndPoint::AddUpdateWorkflowAction()
- TCMSTableEditorEndPoint::GetActionLogAsHTMLTable()
- TCMSTableEditorEndPoint::GetLastActivatedRevision()
- TCMSTableEditorEndPoint::GetLastActivatedRevisionObject()
- TCMSTableEditorEndPoint::GetLastRevisionNumber()
- TCMSTableEditorEndPoint::GetMLTRevisionIds()
- TCMSTableEditorEndPoint::GetRecordChildRevisions()
- TCMSTableEditorEndPoint::GetTransactionOwnership()
- TCMSTableEditorEndPoint::GetTransactionTitle()
- TCMSTableEditorEndPoint::GetWorkflowPreviewPageID()
- TCMSTableEditorEndPoint::InsertForwardLog()
- TCMSTableEditorEndPoint::IsRecordLockedByTransaction()
- TCMSTableEditorEndPoint::IsRevisionManagementActive()
- TCMSTableEditorEndPoint::IsTransactionOwner()
- TCMSTableEditorEndPoint::Publish()
- TCMSTableEditorEndPoint::PublishDelete()
- TCMSTableEditorEndPoint::PublishInsert()
- TCMSTableEditorEndPoint::PublishUpdate()
- TCMSTableEditorEndPoint::RollBack()
- TCMSTableEditorEndPoint::RollBackDelete()
- TCMSTableEditorEndPoint::RollBackInsert()
- TCMSTableEditorEndPoint::RollBackUpdate()
- TCMSTableEditorEndPoint::SaveNewRevision()
- TCMSTableEditorEndPoint::SaveWorkflowActionLog()
- TCMSTableEditorEndPoint::SendOwnershipMovedNotifyEmail()
- TCMSTableEditorEndPoint::SetWorkflowByPass()
- TCMSTableEditorEndPoint::SetWorkflowState()
- TCMSTableEditorEndPoint::UpdatePositionFieldIgnoringWorkflow()
- TCMSTableEditorManager::ActivateRecordRevision()
- TCMSTableEditorManager::AddNewRevision()
- TCMSTableEditorManager::AddNewRevisionFromDatabase()
- TCMSTableEditorManager::AddUpdateWorkflowAction()
- TCMSTableEditorManager::GetLastActivatedRevision()
- TCMSTableEditorManager::IsRevisionManagementActive()
- TCMSTableEditorManager::IsRecordLockedByTransaction()
- TCMSTableEditorManager::Publish()
- TCMSTableEditorManager::RollBack()
- TCMSTableEditorManager::SetWorkflowByPass()
- TCMSTableEditorMedia::_FieldContainsImage()
- TCMSTableEditorMedia::_GetImageRecords()
- TCMSTableEditorMedia::_GetMediaConnectionObject()
- TCMSTableEditorMedia::FetchConnections()
- TCMSTableEditorMedia::LoadFlvInfo()
- TCMSTableEditorMedia::MoveWorkflowImageToMediaPool()
- TCMSTableEditorMedia::RefreshImageOnViddler()
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionForModuleInstances()
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionModuleConnectedTableRecord()
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionModuleConnectedTables()
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionModuleInstance()
- TCMSTableEditorTplPageCmsMasterPageDefSpot::IsRevisonAllowedConnectedTable()
- TCMSTableToClass::getDatabaseConnection()
- TCMSTableToClass::setDatabaseConnection()
- TCMSTableToClass::UpdateAllTables()
- TCMSTableWriter::AddWorkflowFieldsToMLT()
- TCMSTextFieldEndPoint::_callback_cmstextfield_image_flv_parser()
- TCMSTextFieldEndPoint::_callback_cmstextfield_varparser()
- TCMSTextFieldEndPoint::_RemoveProprietaryParameter()
- TCMSTextFieldEndPoint::_ReplaceVariables()
- TCMSTPLModule::getViewMapperConfigForView()
- TCMSTreeNode::ConvertToValidXHTMLLink()
- TCMSUpdateManager::checkIfUpdateHasBeenProcessed()
- TCMSUpdateManager::getBuildNumbersForFolder()
- TCMSUpdateManager::getLatestBuildNumberForFolder()
- TCMSUpdateManager::RunCoreUpdates()
- TCMSURLHistory::FindHistoryId()
- TCMSUser::generateHash()
- TCMSUser::LoadWorkflowEngineStatus()
- TCMSUserInput::AutoAddAuthenticityTokenToForms()
- TCMSUserInput::CallbackAddAuthenticityTokenToForms()
- TCMSUserInput::CheckAuthenticityTokenInInput()
- TCMSUserInput::GenerateNewAuthenticityToken()
- TCMSUserInput::GetAuthenticityToken()
- TCMSUserInput::GetAuthenticityTokenName()
- TCMSUserInput::GetAuthenticityTokenString()
- TCMSUserInput::HasActiveAutoProtectInputViaAuthenticityToken()
- TCMSWizardStep::AddCachePrameters()
- TCMSWizardStep::AddClearCacheTriggers()
- TCMSWizardStep::AllowCaching()
- TCMSWizardStep::TriggerClearCache()
- TPkgCmsTextBlock::GetCacheTrigger()
- TPkgMultiModule_CMSTPLModuleInstance::GetAjaxURLForContainingModule()
- TDataExtranetCore::CacheCommit()
- TDataExtranetCore::IsExtranetUsingCryptedPassword()
- TDataExtranetUser::CommitToSession()
- TDataExtranetUser::GetLinkSendDoubleOptInEmail()
- TDataExtranetUser::GetPasswordSalt()
- TDataExtranetUser::getRedirectToAccessDeniedPageLink()
- TDataExtranetUser::RedirectToAccessDeniedPage()
- TDataMailProfile::GetEMailAddress()
- TGlobal::GetActiveLanguageId()
- TGlobal::GetURLHistory()
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
- TGoogleMapEndPoint::setAPIVersion()
- TGoogleMapEndPoint::showSearchBar()
- THTMLTable::AddClearCacheTriggers()
- THTMLTable::GetCacheRelevantTables()
- THTMLTable::GetClearCacheTriggerTableValue()
- TModelBase::AllowPageCache()
- TModelBase::ClearCache()
- TModelBase::ExecuteExport()
- TModelBase::GetExportLink()
- TModelBase::GetExportView()
- TModelBase::GetModuleType()
- TPkgCmsRouteControllerCmsTplPage::isNonSeoLink()
- TPkgComment::GetDefaultLanguageId()
- TPkgCommentModuleConfig::GetDefaultLanguageId()
- TPkgImageHotspotItemMarker::AddClearCacheTriggers()
- TPkgImageHotspotItemSpot::AddClearCacheTriggers()
- TTools::ConvertMySQL2UnixTimeStamp()
- TTools::DateTime2UnixTimestamp()
- TTools::GenerateEncryptedPassword()
- TTools::GetActivePortal()
- TTools::GetLanguageISOName()
- TTools::GetPageObject()
- TTools::RealNameToURLName()
- TTools::sanitize_filename()
- TTools::sendToHost()
- TTools::sendToHostReturnFull()
- TTools::UnicodeToEntitiesPreservingAscii()
- TTools::UTF8ToEntitiesPreservingAscii()
- TTools::UTF8ToUnicode()
- TViewParser::ClearCache()
- TViewParser::RenderFromCache()
- TViewParser::SetCacheClearTriggers()
- TViewParser::SetCacheParameters()
- TViewParser::UseCaching()
- ViewRenderer::AddMapperFromPath()
- ViewRenderer::resolvePath()

## JavaScript Files and Functions

- bootstrap-colorpicker (new version 3.0.3 located in src/CoreBundle/Resources/public/javascript/jquery/bootstrap-colorpicker-3.0.3).
- Chameleon Flash plugin for CKEditor
- chosen.jquery.js
- flash.js
- html5shiv.js
- jqModal.js 
- jqDnR.js
- jquery.form.js (new version 4.2.2 located in src/CoreBundle/Resources/public/javascript/jquery/jquery-form-4.2.2/jquery.form.min.js).
- jquery.selectboxes.js
- jQueryUI (everything in path src/CoreBundle/Resources/public/javascript/jquery/jQueryUI; drag and drop still used in the template engine).
- maskedinput.js
- mediaManager.js
- pngForIE.htc
- pNotify (new version 3.2.0 located in src/CoreBundle/Resources/public/javascript/pnotify-3.2.0/)
- respond.min.js
- rwd.images.js
- src/CoreBundle/Resources/public/javascript/mainNav.js
- swfobject.js
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
- DeleteMediaDir()
- deleteSelectedMediaDirResponse()
- loadMediaManager()
- PublishViaAjaxCallback()
- SaveNewRevision()
- SetChangedDataMessage()
- showMLTField()
- SwitchEditPortal()
- SwitchEditPortalCallback()

## Translations

- chameleon_system_core.cms_module_cms_search.action_start_index
- chameleon_system_core.cms_module_cms_search.headline
- chameleon_system_core.cms_module_cms_search.select_portal
- chameleon_system_core.cms_module_cms_search.state_index_running
- chameleon_system_core.cms_module_cms_search.state_indexed
- chameleon_system_core.cms_module_header.active_update_counter
- chameleon_system_core.cms_module_header.error_unable_to_create_update_counter
- chameleon_system_core.cms_module_header.msg_created_update_counter
- chameleon_system_core.cms_module_header.msg_update_counter_switched
- chameleon_system_core.cms_module_media_local_import.error_no_read_access_to_path
- chameleon_system_core.cms_module_media_local_import.error_path_not_found
- chameleon_system_core.cms_module_media_local_import.root_path
- chameleon_system_core.cms_module_media_local_import.select_source_path
- chameleon_system_core.cms_module_media_local_media.action_import
- chameleon_system_core.cms_module_media_local_media.file_count
- chameleon_system_core.cms_module_media_local_media.import_result
- chameleon_system_core.cms_module_media_local_media.mark_as_private
- chameleon_system_core.cms_module_media_manager.action_delete
- chameleon_system_core.cms_module_media_manager.action_new_folder
- chameleon_system_core.cms_module_media_manager.action_upload
- chameleon_system_core.cms_module_media_manager.confirm_delete
- chameleon_system_core.cms_module_media_manager.confirm_none_empty_folder_delete
- chameleon_system_core.cms_module_media_manager.confirm_used_image
- chameleon_system_core.cms_module_media_manager.error_folder_name_missing
- chameleon_system_core.cms_module_media_manager.error_no_file_selected
- chameleon_system_core.cms_module_media_manager.error_no_move_source_selected
- chameleon_system_core.cms_module_media_manager.error_no_past_source_selected
- chameleon_system_core.cms_module_media_manager.error_upload_error
- chameleon_system_core.cms_module_media_manager.error_upload_to_root_folder_not_permitted
- chameleon_system_core.cms_module_media_manager.file_id
- chameleon_system_core.cms_module_media_manager.folder
- chameleon_system_core.cms_module_media_manager.image_size
- chameleon_system_core.cms_module_media_manager.msg_attention
- chameleon_system_core.cms_module_media_manager.msg_delete_success
- chameleon_system_core.cms_module_media_manager.msg_file_in_use
- chameleon_system_core.cms_module_media_manager.msg_on_delete_file_in_use
- chameleon_system_core.cms_module_media_manager.msg_select_move_target
- chameleon_system_core.cms_module_media_manager.msg_single_file_upload_success
- chameleon_system_core.cms_module_media_manager.msg_upload_success
- chameleon_system_core.cms_module_media_manager.root_folder
- chameleon_system_core.cms_module_media_manager.upload_more_files
- chameleon_system_core.cms_module_media_manager.used_in_field
- chameleon_system_core.cms_module_media_manager.used_in_record
- chameleon_system_core.cms_module_media_manager.used_in_table
- chameleon_system_core.error.flash_required
- chameleon_system_core.fields.lookup.no_matches
- chameleon_system_core.module_search.action_run_search
- chameleon_system_core.module_search.no_results
- chameleon_system_core.module_search.search_box_headline
- chameleon_system_core.module_search.result_headline
- chameleon_system_core.record_lock.lock_owner_fax
- chameleon_system_core.record_revision.action_confirm_restore_revision
- chameleon_system_core.record_revision.action_create_page_revision
- chameleon_system_core.record_revision.action_load_page_revision
- chameleon_system_core.record_revision.action_load_revision
- chameleon_system_core.record_revision.action_new_revision
- chameleon_system_core.record_revision.action_restore_revision
- chameleon_system_core.record_revision.based_on
- chameleon_system_core.record_revision.confirm_restore_revision
- chameleon_system_core.record_revision.description
- chameleon_system_core.record_revision.header_new_revision
- chameleon_system_core.record_revision.last_used_date
- chameleon_system_core.record_revision.name
- chameleon_system_core.record_revision.new_revision_help
- chameleon_system_core.record_revision.new_revision_number
- chameleon_system_core.record_revision.no_revision_exists
- chameleon_system_core.record_revision.revision_number
- chameleon_system_core.table_editor_files.error_cluster_distribution
- chameleon_system_core.table_editor_media.error_invalid_flv
- chameleon_system_core.template_engine.header_revision

## Database Tables

None.

## Database Fields

None.

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

## Page Definitions

- api.pagedef.php
- CMSCreateSearchIndex.pagedef.php
- CMSCreateSearchIndexPlain.pagedef.php
- pkgCmsLicenseManager.pagedef.php
- versioninfo.pagedef.php
