UPGRADE FROM 6.3 TO 7.0
=======================

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
- CHAMELEON_MEMCACHED_TIMEOUT_IN_MILLISECONDS
- CHAMELEON_SEO_URL_REWRITE_TO_CLEAN
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
- PATH_CMS_CUSTOMER_WORKFLOW_DOCUMENT
- PATH_CMS_CUSTOMER_WORKFLOW_MEDIA
- PATH_CMS_CUSTOMER_WORKFLOW_MEDIA_THUMBS
- PATH_CMS_FONTS
- PATH_CMS_UPDATE
- REQUEST_TRUSTED_PROXIES
- REQUEST_TRUSTED_HEADER_CLIENT_HOST
- REQUEST_TRUSTED_HEADER_CLIENT_IP
- REQUEST_TRUSTED_HEADER_CLIENT_PORT
- REQUEST_TRUSTED_HEADER_CLIENT_PROTO
- TCMSTableEditorEndPoint::DELETE_REFERENCES_REVISION_DATA_WHITELIST_SESSION_VAR
- USE_FILE_BASED_SESSION_HANDLING
- USE_IMAGELAYER
- USE_LIGHTBOX
- USE_LIVE_PAYMENT
- USE_ONLY_COOKIES_FOR_SESSION_ID

## Classes and Interfaces

- ChameleonSystem\core\DatabaseAccessLayer\Workflow\WorkflowQueryModifierOrderBy
- ChameleonSystem\RevisionManagementBundle\ChameleonSystemRevisionManagementBundle
- MTPassThrough
- TCMSFieldWorkflowActionType
- TCMSFieldWorkflowAffectedRecord
- TCMSFieldWorkflowBool
- TCMSFieldWorkflowPublishActive
- TCMSFontImage
- TCMSFontImageList
- TCMSSmartURLHandler_Pagepath
- TCMSListManagerRevisionManagement
- TCMSTableEditorRecordRevision

## Properties

- CMSModuleChooser::$bModuleInstanceIsLockedByWorkflowTransaction
- CMSModuleChooser::$bPageIsLockedByWorkflowTransaction
- TAccessManagerPermissions::$revisionManagement
- TAccessManagerPermissions::$workflowPublish
- TCMSImageEndpoint::$iShowImageFromWorkflow
- TCMSRecord::$bBypassWorkflow
- TCMSRecord::$bDataLoadedFromWorkflow
- TCMSRecordList::$bForceWorkflow
- TCMSRecordList::$bUseGlobalFilterInsteadOfPreviewFilter
- TCMSTableEditorEndPoint::$bBypassWorkflow
- TCMSTableEditorEndPoint::$bWorkflowActive
- TCMSTableEditorEndPoint::$bWorkflowIsUpdateFollowingAnInsert
- TCMSUser::$bWorkflowEngineActive

## Methods

- ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel::isBIgnoreWorkflow
- ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel::setBIgnoreWorkflow
- CMSModuleChooser::LoadWorkflowData
- CMSModuleImageManager::HandleWorkflowOnSetImage
- CMSModulePageTree::GetTransactionDetails
- CMSTemplateEngine::GetLastRevisionNumber
- CMSTemplateEngine::LoadRevisionData
- CMSTemplateEngine::LoadWorkflowData
- gcf_workflowEditedRecordname
- gcf_workflowLastChange
- gcf_workflowStatus
- MTHeader::GetCurrentTransactionInfo
- MTTableEditor::ActivateRevision
- MTTableEditor::AddNewRevision
- MTTableEditor::GetLastRevisionNumber
- MTTableEditor::LoadRevisionData
- MTTableEditor::LoadWorkflowData
- MTTableEditor::PublishViaAjax
- TAccessManager::HasRevisionManagementPermission
- TAccessManager::HasWorkflowEditPermission
- TAccessManager::HasWorkflowPublishPermission
- TAccessManagerPermissions::GetRevisionManagementPermissionStatus
- TAccessManagerPermissions::GetWorkflowPublishStatus
- TAdb*List::EditedRecordsAvailable
- TAdb*List::IsTableWithActiveWorkflow
- TCMSListManagerEndPoint::GetWorkflowRestrictions
- TCMSListManagerFullGroupTable::CallBackWorkflowActionType
- TCMSListManagerFullGroupTable::IsCmsWorkflowTransaction
- TCMSListManagerMLT::CallBackWorkflowConnectionActionType
- TCMSPortalDomain::IsDevelopmentDomain
- TCMSRecord::GetWorkflowMLTFilterQuery
- TCMSRecord::GetWorkflowRestrictionQuery
- TCMSRecord::IsTableWithWorkflow
- TCMSRecord::SetWorkflowByPass
- TCMSTableEditorDocumentEndPoint::MoveWorkflowDocumentToDocumentPool
- TCMSTableEditorEndPoint::ActivateMLTRecordRevisions
- TCMSTableEditorEndPoint::ActivateRecordRevision
- TCMSTableEditorEndPoint::ActivateRecordRevision_Execute
- TCMSTableEditorEndPoint::AddInsertWorkflowAction
- TCMSTableEditorEndPoint::AddNewRevision
- TCMSTableEditorEndPoint::AddNewRevision_Execute
- TCMSTableEditorEndPoint::AddNewRevisionForConnectedPropertyRecords
- TCMSTableEditorEndPoint::AddNewRevisionForMLTConnectedRecords
- TCMSTableEditorEndPoint::AddNewRevisionForSingleFields
- TCMSTableEditorEndPoint::AddNewRevisionFromDatabase
- TCMSTableEditorEndPoint::AddUpdateWorkflowAction
- TCMSTableEditorEndPoint::GetActionLogAsHTMLTable
- TCMSTableEditorEndPoint::GetLastActivatedRevision
- TCMSTableEditorEndPoint::GetLastActivatedRevisionObject
- TCMSTableEditorEndPoint::GetLastRevisionNumber
- TCMSTableEditorEndPoint::GetMLTRevisionIds
- TCMSTableEditorEndPoint::GetRecordChildRevisions
- TCMSTableEditorEndPoint::GetTransactionOwnership
- TCMSTableEditorEndPoint::GetTransactionTitle
- TCMSTableEditorEndPoint::GetWorkflowPreviewPageID
- TCMSTableEditorEndPoint::InsertForwardLog
- TCMSTableEditorEndPoint::IsRecordLockedByTransaction
- TCMSTableEditorEndPoint::IsRevisionManagementActive
- TCMSTableEditorEndPoint::IsTransactionOwner
- TCMSTableEditorEndPoint::Publish
- TCMSTableEditorEndPoint::PublishDelete
- TCMSTableEditorEndPoint::PublishInsert
- TCMSTableEditorEndPoint::PublishUpdate
- TCMSTableEditorEndPoint::RollBack
- TCMSTableEditorEndPoint::RollBackDelete
- TCMSTableEditorEndPoint::RollBackInsert
- TCMSTableEditorEndPoint::RollBackUpdate
- TCMSTableEditorEndPoint::SaveNewRevision
- TCMSTableEditorEndPoint::SaveWorkflowActionLog
- TCMSTableEditorEndPoint::SendOwnershipMovedNotifyEmail
- TCMSTableEditorEndPoint::SetWorkflowByPass
- TCMSTableEditorEndPoint::SetWorkflowState
- TCMSTableEditorEndPoint::UpdatePositionFieldIgnoringWorkflow
- TCMSTableEditorManager::ActivateRecordRevision
- TCMSTableEditorManager::AddNewRevision
- TCMSTableEditorManager::AddNewRevisionFromDatabase
- TCMSTableEditorManager::AddUpdateWorkflowAction
- TCMSTableEditorManager::GetLastActivatedRevision
- TCMSTableEditorManager::IsRevisionManagementActive
- TCMSTableEditorManager::IsRecordLockedByTransaction
- TCMSTableEditorManager::Publish
- TCMSTableEditorManager::RollBack
- TCMSTableEditorManager::SetWorkflowByPass
- TCMSTableEditorMedia::MoveWorkflowImageToMediaPool
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionForModuleInstances
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionModuleConnectedTableRecord
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionModuleConnectedTables
- TCMSTableEditorTplPageCmsMasterPageDefSpot::AddNewRevisionModuleInstance
- TCMSTableEditorTplPageCmsMasterPageDefSpot::IsRevisonAllowedConnectedTable
- TCMSTableWriter::AddWorkflowFieldsToMLT
- TCMSUser::LoadWorkflowEngineStatus

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
