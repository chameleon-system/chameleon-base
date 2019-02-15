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
     * @deprecated since 6.3.0 - revision management is no longer supported
     *
     * {@inheritdoc}
     */
    protected function AddNewRevision_Execute($oFields, $oPostTable, $postData, $sParentId = '')
    {
        return false;
    }

    /**
     * @deprecated since 6.3.0 - revision management is no longer supported
     *
     * load module from spot and make revisions of the module instance and the connected module tables.
     *
     * @param TCMSRecord $oRecordShortInfoData
     * @param array      $postDataFromParentRevision
     */
    protected function AddNewRevisionForModuleInstances($oRecordShortInfoData, $postDataFromParentRevision)
    {
    }

    /**
     * @deprecated since 6.3.0 - revision management is no longer supported
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
     * @deprecated since 6.3.0 - revision management is no longer supported
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
        return false;
    }

    /**
     * @deprecated since 6.3.0 - revision management is no longer supported
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
    }

    /**
     * @deprecated since 6.3.0 - revision management is no longer supported
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
        return false;
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
