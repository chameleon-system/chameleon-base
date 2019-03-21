UPGRADE FROM 6.3 TO 7.0
=======================

# Removed Code Entities

The code entities in this list were marked as deprecated in previous releases and have now been removed.

## Services

## Container Parameters

## Bundle Configuration

## Constants

- CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE
- CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR
- chameleon::REQUEST_TYPE_ASSETS
- chameleon::REQUEST_TYPE_BACKEND
- chameleon::REQUEST_TYPE_BOOT_ONLY
- chameleon::REQUEST_TYPE_FRONTEND
- chameleon::REQUEST_TYPE_UNITTEST
- ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface::REQUEST_TYPE_BOOT_ONLY
- ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface::REQUEST_TYPE_UNITTEST
- MTHeader::CONFIGPARAM_DB_COUNTER
- MTHeader::CONFIGPARAM_TIMESTAMP
- MTHeader::DB_LOGGING_STATE
- MTHeader::TIMESTAMP_CREATED_IN_SESSION
- PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CROSS_DOMAIN_TRACKING
- TCMSCronJob::LOG_FILE
- TCMSMemcache::CACHE_DRIVER_MEMCACHE
- TCMSMemcache::CACHE_DRIVER_MEMCACHED
- TCMSUserInput::FILTER_EMAIL
- TCMSUserInput::FILTER_XSS
- TDataExtranetUser::MAX_SALT_AGE_IN_SECONDS

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

## Translations

## Database Tables

## Database Fields
