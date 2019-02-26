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

- TCMSTableEditorEndPoint::DELETE_REFERENCES_REVISION_DATA_WHITELIST_SESSION_VAR

## Classes and Interfaces

- ChameleonSystem\core\DatabaseAccessLayer\Workflow\WorkflowQueryModifierOrderBy
- ChameleonSystem\RevisionManagementBundle\ChameleonSystemRevisionManagementBundle
- MTPassThrough
- TCMSFieldWorkflowActionType
- TCMSFieldWorkflowAffectedRecord
- TCMSFieldWorkflowBool
- TCMSFieldWorkflowPublishActive
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

- PublishViaAjaxCallback

## Translations

## Database Tables

## Database Fields
