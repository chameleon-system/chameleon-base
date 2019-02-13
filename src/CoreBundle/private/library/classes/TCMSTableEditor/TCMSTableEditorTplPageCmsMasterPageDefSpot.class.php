<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorTplPageCmsMasterPageDefSpot extends TCMSTableEditor
{
    /**
     * makes it possible to modify the contents written to database after the copy
     * is commited.
     */
    protected function OnAfterCopy()
    {
        parent::OnAfterCopy();
        $iTableID = TTools::GetCMSTableId('cms_tpl_module_instance');
        $oTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oCmsTplModuleInstance = $this->oTable->GetFieldCmsTplModuleInstance();
        /** @var $oCmsTplModuleInstance TdbCmsTplModuleInstance */
        if (is_object($oCmsTplModuleInstance) && property_exists($oCmsTplModuleInstance, 'id') && !empty($oCmsTplModuleInstance->id)) {
            $oCmsTplModule = $oCmsTplModuleInstance->GetFieldCmsTplModule();
            if ($oCmsTplModule->fieldIsCopyAllowed) {
                // copy module instance
                $sNewName = TGlobal::Translate('chameleon_system_core.table_editor_page_def_spot.after_copy_name', array('%name%' => $oCmsTplModuleInstance->GetName()));
                $aFieldValueOverloading = array('name' => $sNewName);

                $oTableEditor->Init($iTableID, $oCmsTplModuleInstance->id);
                $oRecordInfo = $oTableEditor->DatabaseCopy(false, $aFieldValueOverloading, $this->bIsCopyAllLanguageValues);
                if ($oRecordInfo && $oRecordInfo->id) {
                    $this->SaveField('cms_tpl_module_instance_id', $oRecordInfo->id);
                }
            }
        }
    }

    /**
     * @deprecated since 6.3.0
     *
     * {@inheritdoc}
     */
    protected function AddNewRevision_Execute($oFields, $oPostTable, $postData, $sParentId = '')
    {
        $oRecordShortInfoData = parent::AddNewRevision_Execute($oFields, $oPostTable, $postData, $sParentId);
        if (false !== $oRecordShortInfoData) {
            $this->AddNewRevisionForModuleInstances($oRecordShortInfoData, $postData);
        }

        return $oRecordShortInfoData;
    }

    /**
     * @deprecated since 6.3.0
     *
     * load module from spot and make revisions of the module instance and the connected module tables.
     *
     * @param TCMSRecord $oRecordShortInfoData
     * @param array      $postDataFromParentRevision
     */
    protected function AddNewRevisionForModuleInstances($oRecordShortInfoData, $postDataFromParentRevision)
    {
        $oCmsTplModuleInstance = $this->oTable->GetFieldCmsTplModuleInstance();
        if (is_object($oCmsTplModuleInstance) && property_exists($oCmsTplModuleInstance, 'id') && !empty($oCmsTplModuleInstance->id)) {
            $oCmsTplModule = $oCmsTplModuleInstance->GetFieldCmsTplModule();
            if (!is_null($oCmsTplModule) && is_object($oCmsTplModule)) {
                $this->AddNewRevisionModuleInstance($oCmsTplModuleInstance, $postDataFromParentRevision, $oRecordShortInfoData);
                if ($oCmsTplModule->fieldRevisionManagementActive) {
                    $this->AddNewRevisionModuleConnectedTables($oCmsTplModule, $oCmsTplModuleInstance, $postDataFromParentRevision, $oRecordShortInfoData);
                }
            }
        }
    }

    /**
     * @deprecated since 6.3.0
     *
     * Make new revision from tables connected to given module.
     *
     * @param TdbCmsTplModule         $oCmsTplModule
     * @param TdbCmsTplModuleInstance $oCmsTplModuleInstance
     * @param TCMSRecord              $postDataFromParentRevision
     * @param array                   $oRecordShortInfoData
     */
    protected function AddNewRevisionModuleConnectedTables($oCmsTplModule, $oCmsTplModuleInstance, $postDataFromParentRevision, $oRecordShortInfoData)
    {
        $oModuleTableConfList = $oCmsTplModule->GetFieldCmsTblConfList();
        $oModuleConnectedTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        while ($oModuleTableConf = $oModuleTableConfList->Next()) {
            if ($this->IsRevisonAllowedConnectedTable($oModuleTableConf)) {
                $oModuleContentRecordList = $this->GetConnectedTableRecords($oModuleTableConf, $oCmsTplModuleInstance);
                while ($oModuleContentRecord = $oModuleContentRecordList->Next()) {
                    /** @var $oModuleContentRecord TCMSRecord */
                    $this->AddNewRevisionModuleConnectedTableRecord($oModuleConnectedTableEditor, $oModuleTableConf, $oModuleContentRecord, $postDataFromParentRevision, $oRecordShortInfoData);
                }
            }
        }
    }

    /**
     * Get all records from given table connected with given module instance.
     *
     * @param TdbCmsTblConf           $oModuleTableConf
     * @param TdbCmsTplModuleInstance $oCmsTplModuleInstance
     *
     * @return TCMSRecordList $oModuleContentRecordList
     */
    protected function GetConnectedTableRecords($oModuleTableConf, $oCmsTplModuleInstance)
    {
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $oModuleTableConf->fieldName.'List');

        /** @var $oModuleContentRecordList TCMSRecordList */
        $oModuleContentRecordList = new $sClassName();
        $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($oModuleTableConf->fieldName)."` WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oCmsTplModuleInstance->id)."'";
        $oModuleContentRecordList->Load($sQuery);

        return $oModuleContentRecordList;
    }

    /**
     * @deprecated since 6.3.0
     *
     * Check if we can make a revision for the given table.
     * Revision is allowed when table field RevisionManagementActive is ture and
     * the value of field name was cms_tpl_module_instance_id.
     *
     * @param TdbCmsTblConf $oModuleTableConf
     *
     * @return bool $bIsRevisionAllowedConnectedTable
     */
    protected function IsRevisonAllowedConnectedTable($oModuleTableConf)
    {
        $bIsRevisionAllowedConnectedTable = false;
        if ('1' == $oModuleTableConf->fieldRevisionManagementActive) {
            $query = "SELECT * FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oModuleTableConf->id)."' AND `name` = 'cms_tpl_module_instance_id'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                $bIsRevisionAllowedConnectedTable = true;
            }
        }

        return $bIsRevisionAllowedConnectedTable;
    }

    /**
     * @deprecated since 6.3.0
     *
     * Make new revision for given table record.
     *
     * @param TdbCmsTblConf $oModuleTableConf
     * @param TCMSRecord    $oModuleContentRecord
     * @param array         $postDataFromParentRevision
     * @param TCMSRecord    $oRecordShortInfoData
     */
    protected function AddNewRevisionModuleConnectedTableRecord($oModuleConnectedTableEditor, $oModuleTableConf, $oModuleContentRecord, $postDataFromParentRevision, $oRecordShortInfoData)
    {
        $oModuleConnectedTableEditor->Init($oModuleTableConf->id, $oModuleContentRecord->id);
        $oModuleFields = $oModuleTableConf->GetFields($oModuleContentRecord);
        $oModuleConnectedTableEditor->AddNewRevisionFromDatabase($oModuleFields, $oModuleContentRecord, $postDataFromParentRevision, $oRecordShortInfoData->id);
    }

    /**
     * @deprecated since 6.3.0
     *
     * Make new revision for given module instance.
     *
     * @param TdbCmsTplModuleInstance $oCmsTplModuleInstance
     * @param array                   $postDataFromParentRevision
     * @param TCMSRecord              $oRecordShortInfoData
     *
     * @return bool $bSuccess
     */
    protected function AddNewRevisionModuleInstance($oCmsTplModuleInstance, $postDataFromParentRevision, $oRecordShortInfoData)
    {
        $iCmsTplModuleInstanceTableID = TTools::GetCMSTableId('cms_tpl_module_instance');
        $oCmsTplModuleInstanceTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oCmsTplModuleInstanceTableEditor->Init($iCmsTplModuleInstanceTableID, $oCmsTplModuleInstance->id);
        $oCmsTplModuleInstanceTableConf = $oCmsTplModuleInstance->GetTableConf();
        /** @var $oPropetyTableConf TCMSTableConf */
        $oFields = $oCmsTplModuleInstanceTableConf->GetFields($oCmsTplModuleInstance);
        /** @var $oFields TIterator */
        $oFields->RemoveItem('name', 'cms_tpl_page_cms_master_pagedef_spot');
        $bSuccess = $oCmsTplModuleInstanceTableEditor->AddNewRevisionFromDatabase($oFields, $oCmsTplModuleInstance, $postDataFromParentRevision, $oRecordShortInfoData->id);

        return $bSuccess;
    }

    /**
     * copies a record using data from database instead of post data.
     *
     * @param bool  $bLanguageCopy
     * @param array $aOverloadedFields fields to copy with given value
     * @param bool  $bCopyAllLanguages Set to true if you want top copy alle language fields
     *
     * @return TCMSstdClass - object from GetObjectShortInfo() method with id, error messages etc
     */
    public function DatabaseCopy($bLanguageCopy = false, $aOverloadedFields = array(), $bCopyAllLanguages = false)
    {
        $this->LoadDataFromDatabase();
        $returnVal = null;
        if (!empty($this->oTable->sqlData['cms_tpl_module_instance_id'])) {
            $returnVal = parent::DatabaseCopy($bLanguageCopy, $aOverloadedFields, $bCopyAllLanguages);
        }

        return $returnVal;
    }
}
