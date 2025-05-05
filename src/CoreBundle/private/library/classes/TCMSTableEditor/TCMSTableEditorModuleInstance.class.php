<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorModuleInstance extends TCMSTableEditor
{
    /**
     * changes the view of a module (deprecated?).
     *
     * @param string $moduleViewFieldName
     *
     * @return TCMSStandardClass
     */
    public function ChangeView($moduleViewFieldName = 'template')
    {
        $returnVal = false;
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('sView') && $oGlobal->UserDataExists('id')) {
            $sView = $oGlobal->GetUserData('sView');
            $this->SaveField($moduleViewFieldName, $sView);
        }

        return $returnVal;
    }

    /**
     * makes it possible to modify the contents written to database after the copy
     * is commited.
     */
    protected function OnAfterCopy()
    {
        parent::OnAfterCopy();
        // copy module content
        $this->CopyModuleContent();
    }

    /**
     * copies all records of tables connected with the module that have a cms_tpl_module_instance_id field
     * global table content isn`t copied.
     */
    protected function CopyModuleContent()
    {
        $oModuleTableConfList = $this->GetModuleInstanceConfigTables();
        // loop through connected module tables
        /** @var $oModuleTableConf TdbCmsTblConf */
        while ($oModuleTableConf = $oModuleTableConfList->Next()) {
            $query = "SELECT * FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oModuleTableConf->id)."' AND `name` = 'cms_tpl_module_instance_id'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) { // field cms_tpl_module_instance_id exists
                $oModuleContentRecordList = $this->GetConnectedTableRecords($oModuleTableConf, $this->sSourceId);
                /** @var $oModuleContentRecord TCMSRecord */
                while ($oModuleContentRecord = $oModuleContentRecordList->Next()) {
                    $oTableEditorManager = TTools::GetTableEditorManager($oModuleTableConf->sqlData['name'], $oModuleContentRecord->id);
                    $oTableEditorManager->DatabaseCopy(false, ['cms_tpl_module_instance_id' => $this->sId], $this->bIsCopyAllLanguageValues);
                }
            }
        }
    }

    /**
     * Get all records from given table connected with given module instance.
     *
     * @param TdbCmsTblConf $oModuleTableConf
     *
     * @return TCMSRecordList $oModuleContentRecordList
     */
    protected function GetConnectedTableRecords($oModuleTableConf, $sModuleInstanceID)
    {
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $oModuleTableConf->fieldName.'List');

        /** @var $oModuleContentRecordList TCMSRecordList */
        $oModuleContentRecordList = new $sClassName();
        $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($oModuleTableConf->fieldName)."` WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sModuleInstanceID)."'";
        $oModuleContentRecordList->Load($sQuery);

        return $oModuleContentRecordList;
    }

    /**
     * copy linked foreign property records.
     *
     * @param TCMSField $oField
     * @param string $sourceRecordID
     */
    public function CopyPropertyRecords($oField, $sourceRecordID)
    {
        $sPropertyTableName = $oField->GetPropertyTableName();
        if ('cms_tpl_page_cms_master_pagedef_spot' != $sPropertyTableName) {
            parent::CopyPropertyRecords($oField, $sourceRecordID);
        }
    }

    /**
     * fetches short record data for processing after an ajaxSave
     * is returned by Save method
     * id and name is always available in the returned object
     * overwrite this method to add custom return data.
     *
     * @return object TCMSstdClass
     */
    public function GetObjectShortInfo($postData = [])
    {
        /** @var $oRecordData TCMSstdClass */
        $oRecordData = parent::GetObjectShortInfo();

        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('sName')) {
            $sName = $oGlobal->GetUserData('sName');
            $oRecordData->name = $sName;
        }

        if ($oGlobal->UserDataExists('sView')) {
            $sView = $oGlobal->GetUserData('sView');
            $oRecordData->view = $sView;
        } else {
            if (array_key_exists('template', $postData)) {
                $oRecordData->view = $postData['template'];
            }
        }

        if ($oGlobal->UserDataExists('_fieldName')) {
            $fieldName = $oGlobal->GetUserData('_fieldName');
            $oRecordData->fieldName = $fieldName;
        }

        return $oRecordData;
    }

    /**
     * {@inheritdoc}
     */
    public function DeleteRecordReferencesFromSource()
    {
        $this->DeleteRecordReferenceModuleContent();
        parent::DeleteRecordReferencesFromSource();
    }

    /**
     * deleted references to this module instance in all tables with a cms_tpl_module_instance_id field.
     */
    protected function DeleteRecordReferenceModuleContent()
    {
        if ($this->IsAllowedDeleteModuleInstance()) {
            $oModuleTableConfList = $this->GetModuleInstanceConfigTables();
            // loop through connected module tables
            /** @var $oModuleTableConf TdbCmsTableConf */
            while ($oModuleTableConf = $oModuleTableConfList->Next()) {
                $query = "SELECT * FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oModuleTableConf->id)."' AND `name` = 'cms_tpl_module_instance_id'";
                $result = MySqlLegacySupport::getInstance()->query($query);
                if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) { // field cms_tpl_module_instance_id exists
                    $oModuleContentRecordList = $this->GetConnectedTableRecords($oModuleTableConf, $this->sId);
                    /** @var $oModuleContentRecord TCMSRecord */
                    while ($oModuleContentRecord = $oModuleContentRecordList->Next()) {
                        $oTableEditorManager = TTools::GetTableEditorManager($oModuleTableConf->sqlData['name'], $oModuleContentRecord->id);
                        $oTableEditorManager->Delete();
                    }
                }
            }
        }
    }

    protected function GetModuleInstanceConfigTables()
    {
        $oModuleTableConfList = new TIterator();
        $oCmsTplModule = $this->oTable->GetFieldCmsTplModule();
        if (!is_null($oCmsTplModule)) {
            $oModuleTableConfList = $oCmsTplModule->GetFieldCmsTblConfList();
        }

        return $oModuleTableConfList;
    }

    /**
     * deletes the record and all language childs; updates all references to this record.
     *
     * @param int $sId
     */
    public function Delete($sId = null)
    {
        if (!is_null($sId)) {
            if ($this->IsAllowedDeleteModuleInstance()) {
                parent::Delete($sId);
            }
        }
    }

    protected function IsAllowedDeleteModuleInstance()
    {
        $bIsAllowed = true;
        if ($bIsAllowed) {
            $bIsAllowed = !$this->IsModuleInstanceConnectedToPage(true);
        }

        if ($bIsAllowed) {
            $bIsAllowed = !$this->isModuleInstanceContainModuleInstanceField();
        }

        return $bIsAllowed;
    }

    protected function isModuleInstanceContainModuleInstanceField()
    {
        $bModuleInstanceField = false;
        $oFieldType = TdbCmsFieldType::GetNewInstance();
        $oFieldType->LoadFromField('constname', 'CMSFIELD_MODULEINSTANCE');
        if ($oFieldType) {
            $oTableCmsTplPageCmsMasterPagedefSpot = TdbCmsTblConf::GetNewInstance();
            $oTableCmsTplPageCmsMasterPagedefSpot->LoadFromField('name', 'cms_tpl_page_cms_master_pagedef_spot');
            $sQuery = "SELECT `cms_field_conf`.*, `cms_tbl_conf`.`name` AS tableName  FROM `cms_field_conf`
            LEFT JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
            WHERE `cms_field_type_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oFieldType->id)."'
            AND `cms_field_conf`.`cms_tbl_conf_id` != '".MySqlLegacySupport::getInstance()->real_escape_string($oTableCmsTplPageCmsMasterPagedefSpot->id)."'";

            $oCmsFieldConfigList = TdbCmsFieldConfList::GetList($sQuery);

            while ($oCmsFieldConfig = $oCmsFieldConfigList->Next()) {
                $oTable = TdbCmsTblConf::GetNewInstance($oCmsFieldConfig->fieldCmsTblConfId);
                if ($oTable) {
                    $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $oTable->fieldName).'List';
                    $sFunctionNme = TCMSTableToClass::GetClassName('GetListFor', $oCmsFieldConfig->fieldName);
                    $oConnectedRecordList = call_user_func_array([$sClassName, $sFunctionNme], [$this->sId]);
                    if ($oConnectedRecordList && $oConnectedRecordList->Length() > 0) {
                        $bModuleInstanceField = true;
                        break;
                    }
                }
            }
        }

        return $bModuleInstanceField;
    }

    protected function IsModuleInstanceConnectedToPage($bAllowDelete = false)
    {
        $bIsModuleInstanceConnectedToPage = false;
        $oConnectedPageSpotList = TdbCmsTplPageCmsMasterPagedefSpotList::GetListForCmsTplModuleInstanceId($this->sId);
        if ($bAllowDelete) {
            if ($oConnectedPageSpotList->Length() > 1) {
                $bIsModuleInstanceConnectedToPage = true;
            }
        } else {
            if ($oConnectedPageSpotList->Length() > 0) {
                $bIsModuleInstanceConnectedToPage = true;
            }
        }

        return $bIsModuleInstanceConnectedToPage;
    }
}
