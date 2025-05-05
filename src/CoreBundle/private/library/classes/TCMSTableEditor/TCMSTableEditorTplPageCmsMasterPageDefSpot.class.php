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
                $sNewName = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_page_def_spot.after_copy_name', ['%name%' => $oCmsTplModuleInstance->GetName()]);
                $aFieldValueOverloading = ['name' => $sNewName];

                $oTableEditor->Init($iTableID, $oCmsTplModuleInstance->id);
                $oRecordInfo = $oTableEditor->DatabaseCopy(false, $aFieldValueOverloading, $this->bIsCopyAllLanguageValues);
                if ($oRecordInfo && $oRecordInfo->id) {
                    $this->SaveField('cms_tpl_module_instance_id', $oRecordInfo->id);
                }
            }
        }
    }

    /**
     * Get all records from given table connected with given module instance.
     *
     * @param TdbCmsTblConf $oModuleTableConf
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
     * copies a record using data from database instead of post data.
     *
     * @param bool $bLanguageCopy
     * @param array $aOverloadedFields fields to copy with given value
     * @param bool $bCopyAllLanguages Set to true if you want top copy alle language fields
     *
     * @return TCMSstdClass - object from GetObjectShortInfo() method with id, error messages etc
     */
    public function DatabaseCopy($bLanguageCopy = false, $aOverloadedFields = [], $bCopyAllLanguages = false)
    {
        $this->LoadDataFromDatabase();
        $returnVal = null;
        if (!empty($this->oTable->sqlData['cms_tpl_module_instance_id'])) {
            $returnVal = parent::DatabaseCopy($bLanguageCopy, $aOverloadedFields, $bCopyAllLanguages);
        }

        return $returnVal;
    }
}
