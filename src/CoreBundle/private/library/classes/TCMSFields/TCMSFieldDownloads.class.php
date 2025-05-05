<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/****************************************************************************
 * Download files
/***************************************************************************/
class TCMSFieldDownloads extends TCMSFieldLookupMultiselect
{
    protected $oTableConf;

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDownloads';

    public function GetHTML()
    {
        $this->oTableConf = $this->oTableRow->GetTableConf();

        $html = '<input type="hidden" id="'.TGlobalBase::OutHTML($this->name).'" name="'.TGlobalBase::OutHTML($this->name).'" value="'.TGlobalBase::OutHTML($this->data).'" />
      <div>';

        $html .= TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.link.open_document_manager'), "javascript:loadDocumentManager('".$this->recordId."','".$this->oTableConf->id."','".$this->name."');", 'fas fa-file');
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
      <div class=\"pt-2\">\n";
        while ($oDownload = $oDownloads->Next()) {
            /** @var $oDownload TCMSDownloadFile */
            $tdWidth = 50;
            if (true === $bReadOnly) {
                $tdWidth = 100;
            }

            $html .= '
           <div id="documentManager_'.$this->name.'_'.$oDownload->id.'">
             <table class="table table-striped">
              <tr>
               <td style="width: '.$tdWidth.'%">'.$oDownload->getDownloadHtmlTag().'</td>';

            if (false === $bReadOnly) {
                $html .= '<td style="width: 50%">';

                $deleteButton = '<button type="button" class="btn btn-danger btn-sm mr-2" onClick="if(confirm(\'%s\')){removeDocument(\'%s\',\'%s\',\'%s\',\'%s\')}">
                                    <i class="far fa-trash-alt mr-2"></i>%s
                                </button>';
                $html .= sprintf($deleteButton,
                    TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_download.confirm_removal')),
                    TGlobal::OutJS($this->name),
                    TGlobal::OutJS($oDownload->id),
                    TGlobal::OutJS($this->recordId),
                    TGlobal::OutJS($this->oTableConf->id),
                    TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_download.remove'))
                );

                $detailsButton = '<button type="button" class="btn info btn-sm" onClick="CreateModalIFrameDialog(\'%s?tableid=%s&pagedef=tableeditorPopup&id=%s\', 0, 0, \'%s\');">
                                    <i class="fas fa-edit mr-2"></i>%s
                                </button>';
                $html .= sprintf($detailsButton,
                    TGlobal::OutJS(PATH_CMS_CONTROLLER),
                    TGlobal::OutJS($oDocumentTableConf->id),
                    TGlobal::OutJS($oDownload->id),
                    TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_download.document_details')),
                    TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_download.document_details'))
                );

                $html .= '</td>';
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
     * {@inheritdoc}
     */
    public function GetMLTTableName($aFieldData = [])
    {
        $name = $aFieldData['name'] ?? $this->name;

        return parent::GetMLTTableName(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function GetForeignTableName()
    {
        return 'cms_document';
    }

    /**
     * {@inheritdoc}
     */
    protected function GetConnectedTableNameFromFieldConfig($aFieldSQLData, $sParameterKey = 'connectedTableName')
    {
        return $this->GetForeignTableName();
    }

    /**
     * {@inheritdoc}
     */
    public function GetConnectedTableName($bExistingCount = true)
    {
        return $this->GetForeignTableName();
    }

    /**
     * {@inheritdoc}
     */
    protected function GetConnectedTableNameFromSQLData($aNewFieldData)
    {
        return $this->GetForeignTableName();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnectedTableNameFromDefinition(): ?string
    {
        return $this->GetForeignTableName();
    }

    /**
     * Get an array of either posted data or data from db if nothings has been posted.
     *
     * @return array
     */
    protected function GetRecordsConnectedArrayFrontend()
    {
        $aData = [];
        $sForeignTableName = $this->GetForeignTableNameFrontend();
        $sMLTTableName = $this->GetMLTTableNameFrontend();
        $iCounter = 0;
        if (is_array($this->data) && count($this->data) > 0) {
            // we assume data was already posted
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
        return ['name', 'description'];
    }

    /**
     * @return string
     */
    protected function GetForeignTableNameFrontend()
    {
        return $this->GetForeignTableName();
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
                        $aData = [];
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
                    // upload document already when its valid so user doesn't have to upload it again when something else goes wrong
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
            $aFileUploadData = ['name' => $_FILES[$this->name.'document']['name'][$sKey], 'type' => 'application/octet-stream', 'tmp_name' => $_FILES[$this->name.'document']['tmp_name'][$sKey], 'error' => 0, 'size' => filesize($_FILES[$this->name.'document']['tmp_name'][$sKey])];
            $oMediaTableConf = new TCMSTableConf(); /* @var $oMediaTableConf TCMSTableConf */
            $oMediaTableConf->LoadFromField('name', 'cms_document');
            $oMediaManagerEditor = new TCMSTableEditorDocument();
            $oMediaManagerEditor->AllowEditByAll(true);
            $oMediaManagerEditor->Init($oMediaTableConf->id);
            $oMediaManagerEditor->SetUploadData($aFileUploadData, true);
            $aDocument = ['description' => $_FILES[$this->name.'document']['name'][$sKey]];
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
            $this->oTableConf = $this->oTableRow->GetTableConf();
        }
        if (is_array($this->data) && array_key_exists('x', $this->data)) {
            unset($this->data['x']);
        }
        $sForeignTableName = $this->GetForeignTableNameFrontend();
        $sMLTTableName = $this->GetMLTTableNameFrontend();
        if (!empty($sForeignTableName)) {
            $aConnectedRecordIdsToDelete = [];
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
                    if (array_key_exists('id', $aRow) && TTools::RecordExistsArray($sForeignTableName, ['id' => $aRow['id']])) {
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
                    $oMediaTableConf = new TCMSTableConf();
                    $oMediaTableConf->LoadFromField('name', 'cms_document');
                    $oMediaManagerEditor = new TCMSTableEditorDocument();
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
