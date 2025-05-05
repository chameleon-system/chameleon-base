<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;
use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineTransformableInterface;

/**
 * The image pool.
 * /**/
class TCMSFieldMedia extends TCMSField implements DoctrineTransformableInterface
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldMedia';

    /**
     * @var bool
     */
    protected $bPkgCmsFormImagesHadErrors = false;

    /**
     * the table config db object.
     *
     * @var TCMSTableConf
     */
    public $oTableConf;

    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $default = explode(',', $this->oDefinition->sqlData['field_default_value']);
        $defaultEscaped = array_map(static fn (string $item) => sprintf("'%s'", trim($item)), $default);
        $parameters = [
            'source' => get_class($this),
            'type' => 'array',
            'docCommentType' => 'array<string>',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => sprintf('[%s]', implode(', ', $defaultEscaped)),
            'allowDefaultValue' => true,
            'getterName' => 'get'.$this->snakeToPascalCase($this->name),
            'setterName' => 'set'.$this->snakeToPascalCase($this->name),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/default.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/default.methods.php.twig', $parameters)->render();

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            $this->getDoctrineDataModelXml($namespace),
            [],
            true
        );
    }

    protected function getDoctrineDataModelXml(string $namespace): string
    {
        return $this->getDoctrineRenderer('mapping/string.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'simple_array',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => $this->oDefinition->sqlData['field_default_value'],
            'length' => '' === $this->oDefinition->sqlData['length_set'] ? 255 : $this->oDefinition->sqlData['length_set'],
        ])->render();
    }

    public function GetReadOnly()
    {
        parent::GetReadOnly();

        return $this->getHtmlRenderedHtml(true);
    }

    public function GetHTML()
    {
        parent::GetHTML();

        return $this->getHtmlRenderedHtml(false);
    }

    /**
     * get html fore read only or edit mode.
     *
     * @param bool $bReadOnly
     *
     * @return string
     */
    protected function getHtmlRenderedHtml($bReadOnly)
    {
        $oImages = $this->oTableRow->GetImages($this->name, true);
        parent::GetHTML();
        $aImageData = [];
        $iPosition = 0;
        $this->oTableConf = $this->oTableRow->GetTableConf();

        /* @var $oImage TCMSImage */
        while ($oImage = $oImages->Next()) {
            $oViewRenderer = $this->getViewRenderer();
            $oViewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.media_field_image_box');
            $oViewRenderer->AddMapper(new TPkgCmsTextfieldImage());
            $oViewRenderer->AddSourceObject('sFieldName', $this->name);
            $oViewRenderer->AddSourceObject('sTableId', $this->oTableConf->id);
            $oViewRenderer->AddSourceObject('sRecordId', $this->recordId);
            $oViewRenderer->AddSourceObject('iPosition', $iPosition);
            $oViewRenderer->AddSourceObject('bReadOnly', $bReadOnly);
            $oViewRenderer->AddSourceObject('emptyImageUrl', CHAMELEON_404_IMAGE_PATH_BIG);

            $iWidth = 0;
            $iHeight = 0;
            if (isset($oImage->aData) && isset($oImage->aData['height'])) {
                $iHeight = $oImage->aData['height'];
            }
            if (isset($oImage->aData) && isset($oImage->aData['width'])) {
                $iWidth = $oImage->aData['width'];
            }
            if (0 == $iHeight) {
                $iHeight = 150;
            }
            if (0 == $iWidth) {
                $iWidth = 150;
            }
            $oViewRenderer->AddSourceObject('oImage', $oImage); // full image (not resized yet)
            $oViewRenderer->AddSourceObject('sFullImageURL', $oImage->GetFullURL());
            $oViewRenderer->AddSourceObject(
                'aTagProperties',
                [
                    'width' => $iWidth + 6,
                    'height' => $iHeight + 6,
                    'cmsshowfull' => '1',
                ]
            );
            $aImageData[] = $oViewRenderer->Render('TCMSFieldMedia/mediaSingleItem.html.twig', null, false);
            ++$iPosition;
        }

        $oViewRenderer = $this->getViewRenderer();
        $oViewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.media_multi_field');
        $oViewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.media_field');
        $oViewRenderer->AddSourceObject('sHtmlHiddenFields', $this->_GetHiddenField());
        $oViewRenderer->AddSourceObject('sFieldName', $this->name);
        $oViewRenderer->AddSourceObject('sTableId', $this->oTableConf->id);
        $oViewRenderer->AddSourceObject('sRecordId', $this->recordId);
        $oViewRenderer->AddSourceObject('aImageBoxHtml', $aImageData);

        return $oViewRenderer->Render('TCMSFieldMedia/mediaMultipleItems.html.twig', null, false);
    }

    public function GetHTMLExport()
    {
        $imageURLs = '';
        $oImages = $this->oTableRow->GetImages($this->name, true);
        $oImages->GoToStart();

        while ($oImage = $oImages->Next()) {
            /** @var $oImage TCMSImage */
            if ($oImage->id > 1000 || !is_numeric($oImage->id)) {
                $imageURLs .= $oImage->GetFullURL().'<br />';
            }
        }

        return $imageURLs;
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }

    /**
     * checks if field is mandatory and if field content is valid
     * overwrite this method to add your field based validation
     * you need to add a message to TCMSMessageManager for handling error messages
     * <code>
     * <?php
     *   $oMessageManager = TCMSMessageManager::GetInstance();
     *   $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
     *   $oMessageManager->AddMessage($sConsumerName,'TABLEEDITOR_FIELD_IS_MANDATORY');
     * ?>
     * </code>.
     *
     * @return bool - returns false if field is mandatory and field content is empty or data is not valid
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid) {
            $pattern = '/^([0-9]+|,|[a-zA-Z]+|-+)*$/';
            if ($this->HasContent() && !preg_match($pattern, $this->data)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_MEDIA_NOT_VALID', ['sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle]);
            }
        }

        return $bDataIsValid;
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
        $bHasMandatoryContent = true;
        $bIsMandatory = $this->IsMandatoryField();
        $sDefaulValue = $this->oDefinition->fieldFieldDefaultValue;
        $aDefaulValue = explode(',', $sDefaulValue);
        if (is_array($this->data)) {
            $aNewValue = $this->data;
        } else {
            $sNewValue = $this->data;
            $aNewValue = explode(',', $sNewValue);
        }
        if (count($aNewValue) == count($aDefaulValue)) {
            foreach ($aNewValue as $iIndex => $sValue) {
                if ($sValue == $aDefaulValue[$iIndex]) {
                    if ($bIsMandatory) {
                        $bHasMandatoryContent = false;
                    }
                } else {
                    if (!$bIsMandatory) {
                        $bHasContent = true;
                    }
                }
            }
            if ($bIsMandatory) {
                $bReturnValue = $bHasMandatoryContent;
            } else {
                $bReturnValue = $bHasContent;
            }
        } else {
            $bReturnValue = false;
        }

        return $bReturnValue;
    }

    public function UploadImage($sKey)
    {
        if (is_array($this->data) && array_key_exists($sKey, $this->data)) {
            $oFile = TCMSFile::GetInstance($_FILES[$this->name.'image']['tmp_name'][$sKey]);
            if ($oFile) {
                $oFile->sExtension = strtolower(substr($_FILES[$this->name.'image']['name'][$sKey], strpos($_FILES[$this->name.'image']['name'][$sKey], '.') + 1));
                if ($oFile->IsValidCMSImage()) {
                    $aSizeOfImage = getimagesize($oFile->sPath);
                    $oFile->Load($oFile->sPath);
                    $sContentType = image_type_to_mime_type($aSizeOfImage[2]);
                    $aImageFileData = ['name' => $_FILES[$this->name.'image']['name'][$sKey], 'type' => $sContentType, 'size' => $oFile->dSizeByte, 'tmp_name' => $oFile->sPath, 'error' => 0];
                    $oMediaTableConf = new TCMSTableConf();
                    $oMediaTableConf->LoadFromField('name', 'cms_media');
                    $oMediaManagerEditor = new TCMSTableEditorMedia();
                    $oMediaManagerEditor->AllowEditByAll(true);
                    $oMediaManagerEditor->Init($oMediaTableConf->id);
                    $oMediaManagerEditor->SetUploadData($aImageFileData, true);
                    $aImage = ['description' => $_FILES[$this->name.'image']['name'][$sKey], 'cms_media_id' => 1];
                    $oMediaManagerEditor->Save($aImage);
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
     * upload images and set ids to owning form.
     */
    public function PkgCmsFormTransformFormDataBeforeSave($oForm)
    {
        if ($this->PkgCmsFormUploadedImageDataIsValid()) {
            if (is_array($this->data) && array_key_exists('x', $this->data)) {
                unset($this->data['x']);
            }
            if (is_array($this->data) && count($this->data) > 0) {
                foreach ($this->data as $key => $value) {
                    // upload image already when its valid so user doesn't have to upload it again when something else goes wrong
                    $sNewMediaId = $this->UploadImage($key);
                    if ($sNewMediaId) {
                        $this->data[$key]['cms_media_id'] = $sNewMediaId;
                    }
                }
            }
        }

        return $this->data;
    }

    /**
     * Get additional view data for the render method.
     *
     * @return array
     */
    protected function GetAdditionalViewData()
    {
        $aAdditionalViewData = parent::GetAdditionalViewData();
        $aAdditionalViewData['aRecordsConnected'] = $this->GetRecordsConnectedArrayFrontend();

        return $aAdditionalViewData;
    }

    /**
     * convert form data from frontend if needed.
     */
    public function PkgCmsFormPreGetSQLHook()
    {
        if (is_array($this->data)) {
            $aCmsIds = [];
            foreach ($this->data as $key => $value) {
                $aCmsIds[$key] = $value['cms_media_id'];
            }
            $this->data = implode(',', $aCmsIds);
        }
    }

    /**
     * Get an array of either posted data or data from db if nothings has been posted.
     *
     * @return array
     */
    protected function GetRecordsConnectedArrayFrontend()
    {
        $aData = [];
        $iCounter = 0;
        if (is_array($this->data) && count($this->data) > 0) {
            // we assume data was already posted
            foreach ($this->data as $aRow) {
                $aData[$iCounter] = $aRow;
                ++$iCounter;
            }
        } else {
            if (!empty($this->oTableRow->id)) {
                $this->oTableConf = $this->oTableRow->GetTableConf();
                if (array_key_exists($this->name, $this->oTableRow->sqlData)) {
                    /* @var $oImages TIterator */
                    $oImages = $this->oTableRow->GetImages($this->name, true);
                    /* @var $oImage TCMSIMage */
                    while ($oImage = $oImages->Next()) {
                        $aData[$iCounter]['cms_media_id'] = $oImage->id;
                        ++$iCounter;
                    }
                } else {
                    $aData[$iCounter]['cms_media_id'] = '1';
                }
            } else {
                // we haven't got a record yet, get default value for field and fake it...
                $sDefault = $this->oDefinition->sqlData['field_default_value'];
                $aValues = explode(',', $sDefault);
                foreach ($aValues as $sImageId) {
                    $aData[$iCounter]['cms_media_id'] = $sImageId;
                    ++$iCounter;
                }
            }
        }

        return $aData;
    }

    /**
     * check uploaded image(s) and throw messages if something is wrong.
     *
     * @return bool - returns true if everything is OK
     */
    public function PkgCmsFormDataIsValid()
    {
        // we handle this via PkgCmsFormUploadedImageDataIsValid() already before uploading images
        if (!$this->bPkgCmsFormImagesHadErrors) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check uploaded image(s) and throw messages if something is wrong.
     *
     * @return bool - returns true if everything is OK
     */
    public function PkgCmsFormUploadedImageDataIsValid()
    {
        $bIsValid = true;
        if (is_array($this->data) && count($this->data) > 0) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TdbPkgCmsForm::MSG_MANAGER_BASE.'-FIELD_'.$this->name;
            foreach ($this->data as $key => $value) {
                if (!array_key_exists('presaveimage', $value)) {
                    if (array_key_exists($this->name.'image', $_FILES) && array_key_exists($key, $_FILES[$this->name.'image']['name']) && UPLOAD_ERR_NO_FILE != $_FILES[$this->name.'image']['error'][$key]) {
                        if (UPLOAD_ERR_OK != $_FILES[$this->name.'image']['error'][$key]) {
                            $bIsValid = false;
                            $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE1');
                            break;
                        } else {
                            if (is_uploaded_file($_FILES[$this->name.'image']['tmp_name'][$key])) {
                                $oFile = TCMSFile::GetInstance($_FILES[$this->name.'image']['tmp_name'][$key]);
                                $oFile->sExtension = strtolower(substr($_FILES[$this->name.'image']['name'][$key], strpos($_FILES[$this->name.'image']['name'][$key], '.') + 1));
                                if (!$oFile->IsValidCMSImage()) {
                                    $bIsValid = false;
                                    $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE-TYPE');
                                    break;
                                }
                                $aSizeOfImage = getimagesize($oFile->sPath);
                                if ($aSizeOfImage[1] > 1000 or $aSizeOfImage[0] > 1000) {
                                    $bIsValid = false;
                                    $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE-DIMENSIONS', ['allowedWidth' => 1000, 'allowedHeight' => 1000]);
                                }

                                $oConfig = TdbCmsConfig::GetInstance();
                                $imageUploadMaxSize = $oConfig->sqlData['max_image_upload_size'] * 1024;
                                if ($_FILES[$this->name.'image']['size'][$key] > $imageUploadMaxSize) {
                                    $bIsValid = false;
                                    $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE-SIZE', ['fileSize' => $_FILES[$this->name.'image']['size'][$key],  'allowedSize' => $imageUploadMaxSize]);
                                }
                            } else {
                                $bIsValid = false;
                                $oMsgManager->AddMessage($sConsumerName, 'ERROR-INVALID-IMAGE2');
                                break;
                            }
                        }
                    }
                }
            }
        }
        if (!$bIsValid) {
            $this->bPkgCmsFormImagesHadErrors = true;
        }

        return $bIsValid;
    }

    /**
     * update default value of the field.
     *
     * @param string $sFieldDefaultValue
     * @param string $sFieldName
     * @param bool $bUpdateExistingRecords
     */
    protected function UpdateFieldDefaultValue($sFieldDefaultValue, $sFieldName, $bUpdateExistingRecords = false)
    {
        parent::UpdateFieldDefaultValue($sFieldDefaultValue, $sFieldName, $bUpdateExistingRecords);

        // make default value string to an array so we can count and loop
        $aFieldDefaultValue = explode(',', $sFieldDefaultValue);

        $sQuery = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.*
                   FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`';
        $rResult = MySqlLegacySupport::getInstance()->query($sQuery);
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rResult)) {
            // get the old value of the record and make it also to an array so we can count and loop through this one too
            $aOldValues = explode(',', $aRow[$sFieldName]);

            if (count($aOldValues) > count($aFieldDefaultValue)) {
                /*
                * if there are more old values than new values just pop the last values of $aOldValues until
                * the count of $aOldValues and $aFieldDefaultValue is equal
                */
                while (count($aOldValues) > count($aFieldDefaultValue)) {
                    array_pop($aOldValues);
                }
            } else {
                /**
                 * copy the new default values to the end of $aOldValues but keep the original existing values from $aOldValues.
                 *
                 * e.g. $aOldValues = array('324353453', '423fertgh34i', '1', '1')
                 *      $aFieldDefaultValue = array('1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1')
                 *      $aOldValues after foreach loop = array('324353453', '423fertgh34i', '1', '1', '1', '1', '1', '1', '1', '1', '1')
                 */
                foreach ($aFieldDefaultValue as $sKey => $sSingeFieldDefaultValue) {
                    if (!array_key_exists($sKey, $aOldValues)) {
                        $aOldValues[$sKey] = $sSingeFieldDefaultValue;
                    }
                }
            }

            $sNewValue = implode(',', $aOldValues);

            $sUpdateQuery = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                       SET `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sNewValue)."'
                     WHERE `".MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName)."`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['id'])."'";
            MySqlLegacySupport::getInstance()->query($sUpdateQuery);
        }
    }

    /**
     * we want to set the length of the field by the count of default values * 36 + count of comma
     * e.g. default value is "1,1,1,1,1,1" the length will be 221 (6 * 36 + 5)
     * if the default value is empty (not set via post or $aPostData) or something else,
     * the parent function will be called.
     *
     * @param TCMSRecord $oFieldType
     * @param array|null $aPostData
     *
     * @return string
     */
    protected function GetMySQLLengthSet($oFieldType, $aPostData = null)
    {
        $oGlobal = TGlobal::instance();

        if (!is_null($aPostData)) {
            $sFieldDefaultValue = '';
            if (isset($postData['field_default_value'])) {
                $sFieldDefaultValue = $aPostData['field_default_value'];
            }
        } else {
            $sFieldDefaultValue = $oGlobal->GetUserData('field_default_value');
        }

        if (!empty($sFieldDefaultValue)) {
            $aDefaultValue = explode(',', $sFieldDefaultValue);
            $sReturn = (count($aDefaultValue) * 36) + (count($aDefaultValue) - 1);

            return '('.$sReturn.')';
        }

        return parent::GetMySQLLengthSet($oFieldType, $aPostData);
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
