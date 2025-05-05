<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * {@inheritdoc}
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class TCMSFieldPropertyTable_CmsMedia extends TCMSFieldPropertyTable
{
    /**
     * access via ConfigGetFieldMapping.
     *
     * @var array
     */
    private $aConfigMediaToTargetMapping;

    /**
     * access via GetDefaultValue.
     *
     * @var array
     */
    private $aConfigDefaultValues;

    /**
     * max height of images uploaded in frontend.
     */
    public const MAX_IMAGE_HEIGHT = 4000;
    /**
     * max width of images uploaded in frontend.
     */
    public const MAX_IMAGE_WIDTH = 4000;

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldPropertyMedia';

    public function GetHTML()
    {
        $sImageControl = $this->GetImageControlButtons();

        return $sImageControl.parent::GetHTML();
    }

    /**
     * @return string
     */
    protected function GetImageControlButtons()
    {
        $oViewParser = new TViewParser();
        $aData = ['oField' => $this];
        $oViewParser->AddVarArray($aData);

        return $oViewParser->RenderObjectView('vControlButtons', 'TCMSFields/TCMSFieldPropertyTable_CmsMedia');
    }

    /**
     * return true if the category selector should be shown.
     *
     * @return bool
     */
    public function ConfigShowCategorySelector()
    {
        $showCategorySelector = $this->oDefinition->GetFieldtypeConfigKey('bShowCategorySelector');

        return '0' !== $showCategorySelector;
    }

    public function ConfigGetDefaultCategoryId()
    {
        return $this->oDefinition->GetFieldtypeConfigKey('sDefaultCategoryId');
    }

    protected function ConfigGetMediaFieldName()
    {
        $sFieldName = $this->oDefinition->GetFieldtypeConfigKey('sMediaTargetFieldName');
        if (empty($sFieldName)) {
            $sFieldName = 'cms_media_id';
        }

        return $sFieldName;
    }

    /**
     * return array mapping media fields to target table fields.
     *
     * @param string $sSourceField
     *
     * @return string|bool
     */
    protected function ConfigGetFieldMapping($sSourceField)
    {
        if (is_null($this->aConfigMediaToTargetMapping)) {
            $this->aConfigMediaToTargetMapping = [];
            $sMapping = $this->oDefinition->GetFieldtypeConfigKey('mapping');
            if ($sMapping && !empty($sMapping)) {
                $sMapping = str_replace(' ', '', $sMapping);
                $aMapping = explode('|', $sMapping);
                foreach ($aMapping as $sMappingString) {
                    $aTmpParts = explode('=>', $sMappingString);
                    $this->aConfigMediaToTargetMapping[$aTmpParts[0]] = $aTmpParts[1];
                }
            }
        }
        if (array_key_exists($sSourceField, $this->aConfigMediaToTargetMapping)) {
            return $this->aConfigMediaToTargetMapping[$sSourceField];
        } else {
            return false;
        }
    }

    /**
     * @param string $sSourceField
     * @param TCMSRecord $oSourceRecord
     *
     * @return string
     */
    protected function ConfigGetMappedValue($sSourceField, $oSourceRecord)
    {
        return $oSourceRecord->sqlData[$sSourceField];
    }

    protected function ConfigGetTargetDefaults()
    {
        if (is_null($this->aConfigDefaultValues)) {
            $this->aConfigDefaultValues = [];
            $sMapping = $this->oDefinition->GetFieldtypeConfigKey('default');
            if ($sMapping && !empty($sMapping)) {
                $aMapping = explode('|,', $sMapping);
                foreach ($aMapping as $sMappingString) {
                    $aTmpParts = explode('=>', $sMappingString);
                    $aTmpParts[0] = trim($aTmpParts[0]);
                    $this->aConfigDefaultValues[$aTmpParts[0]] = trim($aTmpParts[1]);
                }
            }
        }

        return $this->aConfigDefaultValues;
    }

    public function _GetOpenUploadWindowJS()
    {
        $parentField = $this->getInputFilterUtil()->getFilteredGetInput('field');
        $isInModal = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        $js = "saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutHTML($this->name)."');";
        if (null !== $parentField && '' !== $parentField && '' === $isInModal) {
            $parentIFrame = $parentField.'_iframe';
            $js .= "saveCMSRegistryEntry('_parentIFrame','".TGlobal::OutHTML($parentIFrame)."');
                    TCMSFieldPropertyTableCmsMediaOpenUploadWindow_".TGlobal::OutJS($this->name).'(document.cmseditform.'.TGlobal::OutHTML($this->name)."__cms_media_tree_id.value,'".TGlobal::OutJS($parentIFrame)."');";
        } else {
            $js .= 'TCMSFieldPropertyTableCmsMediaOpenUploadWindow_'.TGlobal::OutJS($this->name).'(document.cmseditform.'.TGlobal::OutHTML($this->name).'__cms_media_tree_id.value);';
        }

        return $js;
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();
        $aRequest = [
            'pagedef' => 'CMSUniversalUploader',
            'mode' => 'media',
            'callback' => 'CMSFieldPropertyTableCmsMediaPostUploadHook_'.$this->name,
            'queueCompleteCallback' => 'CMSFieldPropertyTableCmsMediaQueueCompleteHook_'.$this->name,
        ];
        $singleMode = $this->oDefinition->GetFieldtypeConfigKey('singleMode');
        if (!empty($singleMode)) {
            $aRequest['singleMode'] = '1';
        }
        $parentField = $this->getInputFilterUtil()->getFilteredGetInput('field');
        if (null !== $parentField && '' !== $parentField) {
            $aRequest['parentIFrame'] = $parentField.'_iframe';
            $aRequest['parentIsInModal'] = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        }

        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aRequest);
        $sErrorMessage = TGlobal::OutJS(
            ServiceLocator::get('translator')->trans('chameleon_system_core.field_property_media.error_missing_target')
        );
        $oGlobal = TGlobal::instance();

        $aParam = $oGlobal->GetUserData(null, ['module_fnc', '_rmhist', '_histid', '_fnc']);
        $aParam['pagedef'] = $oGlobal->GetUserData('pagedef');
        $aParam['module_fnc'] = ['contentmodule' => 'ExecuteAjaxCall'];
        $aParam['_fnc'] = 'ConnectImageObject';
        $aParam['callFieldMethod'] = '1';
        $aParam['_fieldName'] = $this->name;
        if (null !== $parentField && '' !== $parentField) {
            $aParam['parentIFrame'] = $parentField.'_iframe';
            $aParam['parentIsInModal'] = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        }

        $sConnectImageURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParam);

        $sOnClickEvent = $this->getOnClickEvent();

        $sHTML = <<<JAVASCRIPTCODE
<script type="text/javascript">
  function TCMSFieldPropertyTableCmsMediaOpenUploadWindow_{$this->name}(mediaTreeID, parentIFrame = '') {
    if(mediaTreeID != '') {
        if(parentIFrame != '') {
          parent.CreateModalIFrameDialogCloseButton('{$sURL}&treeNodeID=' + mediaTreeID);
        } else {
          CreateModalIFrameDialogCloseButton('{$sURL}&treeNodeID=' + mediaTreeID);
        }
    } else {
      toasterMessage('{$sErrorMessage}','ERROR');
    }
  }
  function CMSFieldPropertyTableCmsMediaPostUploadHook_{$this->name}(data,responseMessage) {
    if (data) {
      sMediaOjectId = data;
      // now call method to insert object
      var sConnectImageURL = '{$sConnectImageURL}&cms_media_id='+encodeURIComponent(data);
      GetAjaxCallTransparent(sConnectImageURL, ConnectImageObject_{$this->name});
    } else {
      toasterMessage('Error',responseMessage);
    }
  }
  function ConnectImageObject_{$this->name}(data){
      // disabled the message, because it kills multiple upload handling
      // DisplayAjaxMessage(data);
  }

  function CMSFieldPropertyTableCmsMediaQueueCompleteHook_{$this->name}() {
      // CloseModalIFrameDialog();
      {$sOnClickEvent}
  }
</script>
JAVASCRIPTCODE;

        $aIncludes[] = $sHTML;

        return $aIncludes;
    }

    public function ConnectImageObject()
    {
        $oGlobal = TGlobal::instance();
        $sMediaId = $oGlobal->GetUserData('cms_media_id');
        $aData = [
            $this->GetMatchingParentFieldName() => $this->recordId,
            $this->ConfigGetMediaFieldName() => $sMediaId,
        ];

        // now set defaults
        $aDefaults = $this->ConfigGetTargetDefaults();
        foreach ($aDefaults as $sField => $sValue) {
            $aData[$sField] = $sValue;
        }
        $oImageObject = TdbCmsMedia::GetNewInstance($sMediaId);
        if ($oImageObject && false !== $oImageObject->sqlData) {
            foreach ($oImageObject->sqlData as $sField => $sValue) {
                $sMappedFieldName = $this->ConfigGetFieldMapping($sField);
                if (false !== $sMappedFieldName) {
                    $aData[$sMappedFieldName] = $this->ConfigGetMappedValue($sField, $oImageObject);
                }
            }
        }

        $oTableEditor = TTools::GetTableEditorManager($this->GetPropertyTableName());
        $oReturn = $oTableEditor->oTableEditor->Save($aData, true);
        if ($oReturn) {
            $oTableEditor->oTableEditor->Save($oTableEditor->oTableEditor->oTable->sqlData);
        }

        if (empty($oTableEditor->oTableEditor->sId)) {
            $sReturn = ServiceLocator::get('translator')->trans('chameleon_system_core.field_property_media.error_creating');
        } else {
            $sReturn = ServiceLocator::get('translator')->trans('chameleon_system_core.field_property_media.created');
        }

        return $sReturn;
    }

    /**
     * sets methods that are allowed to be called via URL (ajax calls).
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'ConnectImageObject';
    }

    public function PkgCmsFormPostSaveHook($sId, $oForm)
    {
        if (is_array($this->data) && array_key_exists('x', $this->data)) {
            unset($this->data['x']);
        }
        $sForeignTableName = $this->GetPropertyTableNameFrontend();
        if (!empty($sForeignTableName) && TTools::FieldExists($sForeignTableName, $this->sTableName.'_id')) {
            $aConnectedRecordIdsToDelete = [];
            if (!empty($this->oTableRow->id)) {
                $sSql = 'SELECT *
                           FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'`
                          WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'_id'."`='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."'";
                $rRes = MySqlLegacySupport::getInstance()->query($sSql);
                while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
                    $aConnectedRecordIdsToDelete[$aRow['id']] = $aRow['id'];
                }
            }
            if (is_array($this->data) && count($this->data) > 0) {
                foreach ($this->data as $aRow) {
                    $sRecordId = null;
                    if (array_key_exists('id', $aRow) && TTools::RecordExistsArray(
                        $sForeignTableName,
                        ['id' => $aRow['id'], $this->sTableName.'_id' => $sId]
                    )
                    ) {
                        unset($aConnectedRecordIdsToDelete[$aRow['id']]);
                        $sRecordId = $aRow['id'];
                    } else {
                        unset($aRow['id']);
                    }
                    $oTableEditor = TTools::GetTableEditorManager($sForeignTableName, $sRecordId);
                    $aRow[$this->sTableName.'_id'] = $sId;
                    $oTableEditor->AllowEditByAll(true);
                    $oTableEditor->Save($aRow);
                    $oTableEditor->AllowEditByAll(false);
                }
            }
            if (is_array($aConnectedRecordIdsToDelete) && count($aConnectedRecordIdsToDelete) > 0) {
                foreach (array_keys($aConnectedRecordIdsToDelete) as $sDeleteId) {
                    $oTableEditor = TTools::GetTableEditorManager($sForeignTableName, $sDeleteId);
                    $oTableEditor->AllowDeleteByAll(true);
                    $oTableEditor->Delete($sDeleteId);
                    $oTableEditor->AllowDeleteByAll(false);
                }
            }
        }
    }

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
                    if (!array_key_exists('presaveimage', $value)) {
                        if (array_key_exists($this->name.'image', $_FILES) && array_key_exists(
                            $key,
                            $_FILES[$this->name.'image']['name']
                        ) && UPLOAD_ERR_NO_FILE != $_FILES[$this->name.'image']['error'][$key]
                        ) {
                            if (UPLOAD_ERR_OK != $_FILES[$this->name.'image']['error'][$key]) {
                                $bIsValid = false;
                                $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE');
                                break;
                            } else {
                                if (is_uploaded_file($_FILES[$this->name.'image']['tmp_name'][$key])) {
                                    $oFile = TCMSFile::GetInstance($_FILES[$this->name.'image']['tmp_name'][$key]);
                                    $oFile->sExtension = strtolower(
                                        substr(
                                            $_FILES[$this->name.'image']['name'][$key],
                                            strpos($_FILES[$this->name.'image']['name'][$key], '.') + 1
                                        )
                                    );
                                    if (!$oFile->IsValidCMSImage()) {
                                        $bIsValid = false;
                                        $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE');
                                        break;
                                    }
                                    $aSizeOfImage = getimagesize($oFile->sPath);
                                    if ($aSizeOfImage[1] > self::MAX_IMAGE_HEIGHT or $aSizeOfImage[0] > self::MAX_IMAGE_WIDTH) {
                                        $bIsValid = false;
                                        $oMsgManager->AddMessage(
                                            $sConsumerName,
                                            'ERROR-INVALID-IMAGE-DIMENSIONS',
                                            [
                                                'allowedWidth' => self::MAX_IMAGE_WIDTH,
                                                'allowedHeight' => self::MAX_IMAGE_HEIGHT,
                                            ]
                                        );
                                    }

                                    $oConfig = TdbCmsConfig::GetInstance();
                                    $imageUploadMaxSize = $oConfig->sqlData['max_image_upload_size'] * 1024;
                                    if ($_FILES[$this->name.'image']['size'][$key] > $imageUploadMaxSize) {
                                        $bIsValid = false;
                                        $oMsgManager->AddMessage(
                                            $sConsumerName,
                                            'ERROR-INVALID-IMAGE-SIZE',
                                            [
                                                'fileSize' => $_FILES[$this->name.'image']['size'][$key],
                                                'allowedSize' => $imageUploadMaxSize,
                                            ]
                                        );
                                    }
                                } else {
                                    $bIsValid = false;
                                    $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE');
                                    break;
                                }
                            }
                        } else {
                            $bIsValid = false;
                            $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE');
                            break;
                        }
                    }
                }
            }
        }

        return $bIsValid;
    }

    /**
     * upload images and set ids to owning form.
     */
    public function PkgCmsFormTransformFormDataBeforeSave($oForm)
    {
        $this->PkgCmsFormDataIsValid();
        if (is_array($this->data) && array_key_exists('x', $this->data)) {
            unset($this->data['x']);
        }
        if (is_array($this->data) && count($this->data) > 0) {
            foreach ($this->data as $key => $value) {
                if (array_key_exists('presaveimage', $value)) {
                    if (!empty($value['presaveimage']) && array_key_exists(
                        'pkgFormUploadedImagesByUser',
                        $_SESSION
                    ) && is_array($_SESSION['pkgFormUploadedImagesByUser']) && in_array(
                        $value['presaveimage'],
                        $_SESSION['pkgFormUploadedImagesByUser']
                    )
                    ) {
                        $this->data[$key][$this->ConfigGetMediaFieldName()] = $value['presaveimage'];
                        // now set defaults
                        $aData = [];
                        $aDefaults = $this->ConfigGetTargetDefaults();
                        foreach ($aDefaults as $sField => $sValue) {
                            $aData[$sField] = $sValue;
                        }
                        $oImageObject = TdbCmsMedia::GetNewInstance($value['presaveimage']);
                        if ($oImageObject && false !== $oImageObject->sqlData) {
                            foreach ($oImageObject->sqlData as $sField => $sValue) {
                                $sMappedFieldName = $this->ConfigGetFieldMapping($sField);
                                if (false !== $sMappedFieldName) {
                                    $aData[$sMappedFieldName] = $this->ConfigGetMappedValue($sField, $oImageObject);
                                }
                            }
                        }
                        $this->data[$key] = array_merge($aData, $this->data[$key]);
                    } else {
                        unset($this->data[$key]);
                    }
                } else {
                    // upload image already when its valid so user doesn't have to upload it again when something else goes wrong
                    $sNewMediaId = $this->UploadImage($key);
                    if ($sNewMediaId) {
                        $aData = [];
                        $this->data[$key][$this->ConfigGetMediaFieldName()] = $sNewMediaId;
                        // now set defaults
                        $aDefaults = $this->ConfigGetTargetDefaults();
                        foreach ($aDefaults as $sField => $sValue) {
                            $aData[$sField] = $sValue;
                        }
                        $oImageObject = TdbCmsMedia::GetNewInstance($sNewMediaId);
                        if ($oImageObject && false !== $oImageObject->sqlData) {
                            foreach ($oImageObject->sqlData as $sField => $sValue) {
                                $sMappedFieldName = $this->ConfigGetFieldMapping($sField);
                                if (false !== $sMappedFieldName) {
                                    $aData[$sMappedFieldName] = $this->ConfigGetMappedValue($sField, $oImageObject);
                                }
                            }
                        }
                        $this->data[$key] = array_merge($aData, $this->data[$key]);
                    }
                }
            }
        }

        return $this->data;
    }

    /**
     * @param string $sKey
     *
     * @return bool|string
     */
    public function UploadImage($sKey)
    {
        if (is_array($this->data) && array_key_exists($sKey, $this->data)) {
            $oFile = TCMSFile::GetInstance($_FILES[$this->name.'image']['tmp_name'][$sKey]);
            if ($oFile) {
                $oFile->sExtension = strtolower(
                    substr(
                        $_FILES[$this->name.'image']['name'][$sKey],
                        strpos($_FILES[$this->name.'image']['name'][$sKey], '.') + 1
                    )
                );
                if ($oFile->IsValidCMSImage()) {
                    $aSizeOfImage = getimagesize($oFile->sPath);
                    $oFile->Load($oFile->sPath);
                    $sContentType = image_type_to_mime_type($aSizeOfImage[2]);
                    $aImageFileData = [
                        'name' => $_FILES[$this->name.'image']['name'][$sKey],
                        'type' => $sContentType,
                        'size' => $oFile->dSizeByte,
                        'tmp_name' => $oFile->sPath,
                        'error' => 0,
                    ];
                    $oMediaTableConf = new TCMSTableConf();
                    $oMediaTableConf->LoadFromField('name', 'cms_media');
                    $oMediaManagerEditor = new TCMSTableEditorMedia();
                    $oMediaManagerEditor->AllowEditByAll(true);
                    $oMediaManagerEditor->Init($oMediaTableConf->id);
                    $oMediaManagerEditor->SetUploadData($aImageFileData, true);
                    $aImage = [
                        'description' => $_FILES[$this->name.'image']['name'][$sKey],
                        $this->ConfigGetMediaFieldName() => 1,
                    ];
                    $sMediaTreeId = $this->ConfigGetDefaultCategoryId();
                    if (!empty($sMediaTreeId)) {
                        $aImage['cms_media_tree_id'] = $sMediaTreeId;
                    }

                    try {
                        $oMediaManagerEditor->Save($aImage);
                    } catch (Exception $e) {
                        // we have already added the error message to message manager function this->PkgCmsFormDataIsValid()
                        // echo 'Message: ' .$e->getMessage();
                    }

                    if (!empty($oMediaManagerEditor->sId)) {
                        $_SESSION['pkgFormUploadedImagesByUser'][] = $oMediaManagerEditor->sId;

                        return $oMediaManagerEditor->sId;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get additional view data for the render method.
     *
     * @return array
     */
    protected function GetAdditionalViewData()
    {
        $aAdditionalViewData = parent::GetAdditionalViewData();
        $aAdditionalViewData['aAdditionalFields'] = $this->GetAdditionalFormFieldsFrontend();
        $aAdditionalViewData['sConfigMediaFieldName'] = $this->ConfigGetMediaFieldName();

        return $aAdditionalViewData;
    }

    /**
     * @return array
     */
    protected function GetAdditionalFormFieldsFrontend()
    {
        return ['name'];
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
