<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;

/****************************************************************************
 * Download files
/***************************************************************************/
class TCMSFieldDownloads extends TCMSMLTField
{
    protected $oTableConf = null;

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDownloads';

    public function GetHTML()
    {
        $this->oTableConf = &$this->oTableRow->GetTableConf();

        $html = '<input type="hidden" id="'.TGlobalBase::OutHTML($this->name).'" name="'.TGlobalBase::OutHTML($this->name).'" value="'.TGlobalBase::OutHTML($this->data).'" />
      <div>';

        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.link.open_document_manager'), "javascript:loadDocumentManager('".$this->recordId."','".$this->oTableConf->id."','".$this->name."');", URL_CMS.'/images/icons/page_attachment.gif');
        $html .= '</div>
      <div class="cleardiv">&nbsp;</div>';

        $html .= $this->GetAttachedDocumentListAsHTML();

        $html = '<div>'.$html.'</div>';

        return $html;
    }

    /**
     * get current list of attached documents.
     *
     * @param bool $bReadOnly - indicates if remove button should be hidden
     *
     * @return string
     */
    protected function GetAttachedDocumentListAsHTML($bReadOnly = false)
    {
        $html = '';
        $oDownloads = $this->oTableRow->GetDownloads($this->name);

        $oDocumentTableConf = TdbCmsTblConf::GetNewInstance();
        $oDocumentTableConf->LoadFromField('name', 'cms_document');

        $html .= '<div id="documentManager_'.$this->name."_anchor\">
      <div style=\"margin-top: 10px;\">\n";
        while ($oDownload = &$oDownloads->Next()) {
            /** @var $oDownload TCMSDownloadFile */
            $html .= '
           <div id="documentManager_'.$this->name.'_'.$oDownload->id.'">
             <table border="0" cellpadding="0" cellspacing="0" width="400">
              <tr>
               <td width="*">'.$oDownload->GetDownloadLink().'</td>';

            if (!$bReadOnly) {
                $html .= '<td width="20"><img src="'.URL_CMS.'/images/icons/page_delete.gif" alt="'.TGlobal::Translate('chameleon_system_core.field_download.remove')."\" border=\"0\" style=\"cursor: pointer; cursor: hand;\" onClick=\"if(confirm('".TGlobal::Translate('chameleon_system_core.field_download.confirm_removal')."')){removeDocument('".$this->name."','".$oDownload->id."','".$this->recordId."','".$this->oTableConf->id."')};\" /></td>\n";
                $html .= '<td width="20"><img src="'.URL_CMS.'/images/icons/page_edit.gif" alt="'.TGlobal::Translate('chameleon_system_core.field_download.document_details')."\" border=\"0\" style=\"cursor: pointer; cursor: hand;\" onClick=\"CreateModalIFrameDialog('".PATH_CMS_CONTROLLER.'?tableid='.TGlobal::OutHTML($oDocumentTableConf->id).'&pagedef=tableeditorPopup&id='.TGlobal::OutHTML($oDownload->id)."', 0, 0, '".TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_download.document_details'))."');\" /></td>\n";
            }
            $html .= '</tr>
            </table>
           </div>
            ';
        }
        $html .= "</div>
      </div>\n";

        return $html;
    }

    /**
     * removes any related tables (like mlt tables). the function will be called
     * from the outside whenever there is a change of type FROM this type of field.
     */
    public function DeleteRelatedTables()
    {
        $tableName = $this->GetMLTTableName();
        $query = 'DROP TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($tableName).'`';
        MySqlLegacySupport::getInstance()->query($query);

        $aQuery = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * creates any related tables (like mlt tables). the function will be called
     * from the outside whenever there is a change of type TO this type of field.
     */
    public function CreateRelatedTables($returnDDL = false)
    {
        $sql = '';
        $tableName = $this->GetMLTTableName();
        if (!TGlobal::TableExists($tableName)) {
            $query = 'CREATE TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($tableName)."` (
                  `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `entry_sort` int(11) NOT NULL default '0',
                  PRIMARY KEY ( `source_id` , `target_id` ),
                  INDEX (target_id),
                  INDEX (entry_sort)
                )";

            if (!$returnDDL) {
                MySqlLegacySupport::getInstance()->query($query);
                $aQuery = array(new LogChangeDataModel($query));

                TCMSLogChange::WriteTransaction($aQuery);
            } else {
                $sql .= $query.";\n";
            }
        }
        if ($returnDDL) {
            return $sql;
        }
    }

    /**
     * returns the mlt table name.
     *
     * @return string
     */
    public function GetMLTTableName()
    {
        return $this->sTableName.'_'.$this->name.'_cms_document_mlt';
    }

    public function GetForeignTableName()
    {
        return 'cms_document';
    }

    /**
     * @return string
     */
    public function GetConnectedTableName($bExistingCount = true)
    {
        return 'cms_document';
    }

    /**
     * overwrite to delete the related download mlt table.
     */
    public function DeleteFieldDefinition()
    {
        $this->DeleteRelatedTables();
        parent::DeleteFieldDefinition();
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $this->oTableConf = &$this->oTableRow->GetTableConf();
        $html = $this->GetAttachedDocumentListAsHTML(true);

        return $html;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        $oDownloads = $this->oTableRow->GetDownloads($this->name);
        if ($oDownloads->Length() > 0) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * Get an array of either posted data or data from db if nothings has been posted.
     *
     * @return array
     */
    protected function GetRecordsConnectedArrayFrontend()
    {
        $aData = array();
        $sForeignTableName = $this->GetForeignTableNameFrontend();
        $sMLTTableName = $this->GetMLTTableNameFrontend();
        $iCounter = 0;
        if (is_array($this->data) && count($this->data) > 0) {
            //we assume data was already posted
            foreach ($this->data as $aRow) {
                $aData[$iCounter] = $aRow;
                ++$iCounter;
            }
        } elseif (!empty($this->oTableRow->id)) {
            $oFields = $this->GetFieldsTargetTableFrontend();
            $sSql = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'`
                  LEFT JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.`target_id` = `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'`.`id`
                  WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName)."`.`source_id`='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."'";
            $rRes = MySqlLegacySupport::getInstance()->query($sSql);
            while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
                while ($oField = $oFields->Next()) {
                    if ($oField->fieldName != $this->sTableName.'_id') {
                        $aData[$iCounter][$oField->fieldName] = $aRow[$oField->fieldName];
                    }
                }
                $aData[$iCounter]['id'] = $aRow['id'];
                ++$iCounter;
                $oFields->GoToStart();
            }
        }

        return $aData;
    }

    // -----------------------------------------------------------------------

    /**
     * @return array
     */
    protected function GetAdditionalFormFieldsFrontend()
    {
        return array('name', 'description');
    }

    /**
     * @return string
     */
    protected function GetForeignTableNameFrontend()
    {
        return 'cms_document';
    }

    /**
     * @return string
     */
    protected function GetMLTTableNameFrontend()
    {
        return $this->GetMLTTableName();
    }

    /**
     * @return TIterator
     */
    protected function GetFieldsTargetTableFrontend()
    {
        static $oFields = null;
        if (is_null($oFields)) {
            $oFields = new TIterator();
            $sForeignTableName = $this->GetForeignTableNameFrontend();
            $oTblConf = TdbCmsTblConf::GetNewInstance();
            $oTblConf->LoadFromField('name', $sForeignTableName);
            $oTmpFields = TdbCmsFieldConfList::GetList();
            $oTmpFields->AddFilterString("`cms_tbl_conf_id`='".MySqlLegacySupport::getInstance()->real_escape_string($oTblConf->id)."'");
            while ($oTmpField = $oTmpFields->Next()) {
                $oFields->AddItem($oTmpField);
            }
        }
        $oFields->GoToStart();

        return $oFields;
    }

    /**
     * upload documents and set ids to owning form.
     */
    public function PkgCmsFormTransformFormDataBeforeSave($oForm)
    {
        $this->PkgCmsFormDataIsValid();
        if (is_array($this->data) && array_key_exists('x', $this->data)) {
            unset($this->data['x']);
        }
        if (is_array($this->data) && count($this->data) > 0) {
            foreach ($this->data as $key => $value) {
                if (array_key_exists('presavedocument', $value)) {
                    if (!empty($value['presavedocument']) && array_key_exists('pkgFormUploadedDocumentsByUser', $_SESSION) && is_array($_SESSION['pkgFormUploadedDocumentsByUser']) && in_array($value['presavedocument'], $_SESSION['pkgFormUploadedDocumentsByUser'])
                    ) {
                        $this->data[$key]['id'] = $value['presavedocument'];
                        $aData = array();
                        $oDocumentObject = TdbCmsDocument::GetNewInstance($value['presavedocument']);
                        if ($oDocumentObject && false !== $oDocumentObject->sqlData) {
                            $aAllowedFields = $this->GetAdditionalFormFieldsFrontend();
                            foreach ($oDocumentObject->sqlData as $sField => $sValue) {
                                if (in_array($sField, $aAllowedFields)) {
                                    $aData[$sField] = $sValue;
                                }
                            }
                        }
                        $this->data[$key] = array_merge($aData, $this->data[$key]);
                    } else {
                        unset($this->data[$key]);
                    }
                } else {
                    //upload document already when its valid so user doesn't have to upload it again when something else goes wrong
                    $sNewDocumentId = $this->UploadDocument($key);
                    if ($sNewDocumentId) {
                        $this->data[$key]['id'] = $sNewDocumentId;
                    }
                }
            }
        }

        return $this->data;
    }

    /**
     * Upload a document.
     *
     * @param int $sKey
     *
     * @return bool
     */
    public function UploadDocument($sKey)
    {
        if (is_array($this->data) && array_key_exists($sKey, $this->data)) {
            $aFileUploadData = array('name' => $_FILES[$this->name.'document']['name'][$sKey], 'type' => 'application/octet-stream', 'tmp_name' => $_FILES[$this->name.'document']['tmp_name'][$sKey], 'error' => 0, 'size' => filesize($_FILES[$this->name.'document']['tmp_name'][$sKey]));
            $oMediaTableConf = new TCMSTableConf(); /*@var $oMediaTableConf TCMSTableConf*/
            $oMediaTableConf->LoadFromField('name', 'cms_document');
            $oMediaManagerEditor = new TCMSTableEditorDocument(); /*@var $oMediaManagerEditor TCMSTableEditorMedia*/
            $oMediaManagerEditor->AllowEditByAll(true);
            $oMediaManagerEditor->Init($oMediaTableConf->id);
            $oMediaManagerEditor->SetUploadData($aFileUploadData, true);
            $aDocument = array('description' => $_FILES[$this->name.'document']['name'][$sKey]);
            $aAdditionalFields = $this->GetAdditionalFormFieldsFrontend();
            foreach ($aAdditionalFields as $sFieldName) {
                if (array_key_exists($sFieldName, $this->data[$sKey])) {
                    $aDocument[$sFieldName] = $this->data[$sKey][$sFieldName];
                }
            }
            $sDocumentTreeId = '';
            if (!empty($sDocumentTreeId)) {
                $aDocument['cms_media_tree_id'] = $sDocumentTreeId;
            }
            try {
                $oMediaManagerEditor->Save($aDocument);
            } catch (Exception $e) {
                return false;
            }
            if (!empty($oMediaManagerEditor->sId)) {
                $_SESSION['pkgFormUploadedDocumentsByUser'][] = $oMediaManagerEditor->sId;

                return $oMediaManagerEditor->sId;
            }
        }

        return false;
    }

    /**
     * Manage MLT-Connection to documents.
     *
     * @param string $sId id of the saved record
     */
    public function PkgCmsFormPostSaveHook($sId, $oForm)
    {
        if (!$this->oTableConf) {
            $this->oTableConf = &$this->oTableRow->GetTableConf();
        }
        if (is_array($this->data) && array_key_exists('x', $this->data)) {
            unset($this->data['x']);
        }
        $sForeignTableName = $this->GetForeignTableNameFrontend();
        $sMLTTableName = $this->GetMLTTableNameFrontend();
        if (!empty($sForeignTableName)) {
            $aConnectedRecordIdsToDelete = array();
            if (!empty($this->oTableRow->id)) {
                $sSql = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'`
                  LEFT JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.`target_id` = `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'`.`id`
                  WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName)."`.`source_id`='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."'";
                $rRes = MySqlLegacySupport::getInstance()->query($sSql);
                while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
                    $aConnectedRecordIdsToDelete[$aRow['id']] = $aRow['id'];
                }
            }
            if (is_array($this->data) && count($this->data) > 0) {
                foreach ($this->data as $aRow) {
                    $sRecordId = null;
                    if (array_key_exists('id', $aRow) && TTools::RecordExistsArray($sForeignTableName, array('id' => $aRow['id']))) {
                        unset($aConnectedRecordIdsToDelete[$aRow['id']]);
                        $sRecordId = $aRow['id'];
                    } else {
                        unset($aRow['id']);
                    }
                    if ($sRecordId && !in_array($sRecordId, $aConnectedRecordIdsToDelete)) {
                        $sConnectSql = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName)."` SET `source_id`='".MySqlLegacySupport::getInstance()->real_escape_string($sId)."',`target_id`='".MySqlLegacySupport::getInstance()->real_escape_string($sRecordId)."'";
                        MySqlLegacySupport::getInstance()->query($sConnectSql);
                    }
                }
            }
            if (is_array($aConnectedRecordIdsToDelete) && count($aConnectedRecordIdsToDelete) > 0) {
                foreach (array_keys($aConnectedRecordIdsToDelete) as $sDeleteId) {
                    $sDeleteSql = 'DELETE FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName)."` WHERE `source_id`='".MySqlLegacySupport::getInstance()->real_escape_string($sId)."' AND `target_id`='".MySqlLegacySupport::getInstance()->real_escape_string($sDeleteId)."'";
                    MySqlLegacySupport::getInstance()->query($sDeleteSql);
                    $oMediaTableConf = new TCMSTableConf(); /*@var $oMediaTableConf TCMSTableConf*/
                    $oMediaTableConf->LoadFromField('name', 'cms_document');
                    $oMediaManagerEditor = new TCMSTableEditorDocument(); /*@var $oMediaManagerEditor TCMSTableEditorMedia*/
                    $oMediaManagerEditor->AllowDeleteByAll(true);
                    $oMediaManagerEditor->Init($oMediaTableConf->id, $sDeleteId);
                    $oMediaManagerEditor->Delete($sDeleteId);
                    $oMediaManagerEditor->AllowDeleteByAll(false);
                }
            }
        }
    }

    /**
     * Check for valid upload, filesize and extension.
     *
     * @return bool
     */
    public function PkgCmsFormDataIsValid()
    {
        static $bIsValid = null;
        if (is_null($bIsValid)) {
            if (is_array($this->data) && array_key_exists('x', $this->data)) {
                unset($this->data['x']);
            }
            $bIsValid = parent::PkgCmsFormDataIsValid();
            if ($bIsValid && is_array($this->data) && count($this->data) > 0) {
                $oMsgManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TdbPkgCmsForm::MSG_MANAGER_BASE.'-FIELD_'.$this->name;
                foreach ($this->data as $key => $value) {
                    if (!array_key_exists('presavedocument', $value)) {
                        if (array_key_exists($this->name.'document', $_FILES) && array_key_exists($key, $_FILES[$this->name.'document']['name']) && UPLOAD_ERR_NO_FILE != $_FILES[$this->name.'document']['error'][$key]) {
                            if (UPLOAD_ERR_OK != $_FILES[$this->name.'document']['error'][$key]) {
                                $bIsValid = false;
                                $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-DOCUMENT');
                                break;
                            } else {
                                if (is_uploaded_file($_FILES[$this->name.'document']['tmp_name'][$key])) {
                                    $sFileExtension = strtolower(substr($_FILES[$this->name.'document']['name'][$key], strpos($_FILES[$this->name.'document']['name'][$key], '.') + 1));
                                    $maxSize = TTools::GetUploadMaxSizeBytes();
                                    if (filesize($_FILES[$this->name.'document']['tmp_name'][$key]) > $maxSize) {
                                        $bIsValid = false;
                                        $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-DOCUMENT-TOO-LARGE');
                                        break;
                                    }
                                    $aAllowedFileTypes = TTools::GetCMSFileTypes();
                                    if (!in_array($sFileExtension, $aAllowedFileTypes)) {
                                        $bIsValid = false;
                                        $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-DOCUMENT-UNKNOWN-EXTENSION');
                                    }
                                } else {
                                    $bIsValid = false;
                                    $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-DOCUMENT');
                                    break;
                                }
                            }
                        } else {
                            $bIsValid = false;
                            $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-DOCUMENT');
                            break;
                        }
                    }
                }
            }
        }

        return $bIsValid;
    }

    /**
     * Get additional view data for the render method.
     *
     * @return array
     */
    protected function GetAdditionalViewData()
    {
        $aAdditionalViewData = parent::GetAdditionalViewData();
        $aAdditionalViewData['sForeignTableName'] = $this->GetForeignTableNameFrontend();
        $aAdditionalViewData['aAdditionalFields'] = $this->GetAdditionalFormFieldsFrontend();
        $aAdditionalViewData['aRecordsConnected'] = $this->GetRecordsConnectedArrayFrontend();

        return $aAdditionalViewData;
    }

    public function FetchMLTRecords()
    {
        $foreignTableName = $this->GetForeignTableName();
        $sFilterQuery = $this->GetMLTFilterQuery();
        /** @var $oMLTRecords TCMSRecordList */
        $oMLTRecords = call_user_func(array(TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $foreignTableName).'List', 'GetList'), $sFilterQuery);

        return $oMLTRecords;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        if (!$this->data) {
            return '';
        }

        return $this->getDataAsString('cms_document', 'name');
    }
}
