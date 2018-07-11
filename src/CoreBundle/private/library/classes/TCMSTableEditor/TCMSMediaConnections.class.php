<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * found image connections.
/**/
class TCMSMediaConnections
{
    public $table = null;
    public $tableName = null;
    public $id = null;
    public $fieldName = null;
    public $fieldTranslationName = null;
    public $fieldVal = null;
    public $imagesFieldNewVal = null;
    public $recordName = null;
    /**
     * field definition of the item in which the image is.
     *
     * @var TCMSFieldDefinition
     */
    public $oFieldDefinition = null;

    public $_fileID = null;

    /**
     * Remove this connection.
     *
     * @param string $fileID
     */
    public function Delete($fileID)
    {
        $this->_fileID = $fileID;
        $oFieldType = $this->oFieldDefinition->GetFieldType();
        switch ($oFieldType->sqlData['constname']) {
            case 'CMSFIELD_TABLELIST':
                $this->_DelType_TABLELIST();
                break;
            case 'CMSFIELD_MULTITABLELIST':
                $this->_DelType_MULTITABLELIST();
                break;
            case 'CMSFIELD_MEDIA':
            case 'CMSFIELD_EXTENDEDTABLELIST_MEDIA':
                $this->_DelType_MEDIA();
                break;
            case 'CMSFIELD_WYSIWYG':
            case 'CMSFIELD_WYSIWYG_LIGHT':
                $this->_DelType_TEXT();
                break;
            default:
                $errorMsg = 'Warning: Do not know how to remove an image in field type '.$oFieldType->sqlData['constname']." <br>\n";
                $errorMsg .= print_r($this, true);
                trigger_error($errorMsg, E_USER_WARNING);
                break;
        }
    }

    /**
     * Clean cache key of a connected record.
     */
    public function CleanConnectedRecordCaching()
    {
        TCacheManager::PerformeTableChange($this->table, $this->id);
    }

    /**
     * callback method to remove a connection to a deleted cms media file
     * resets to default value.
     */
    protected function _DelType_TABLELIST()
    {
        // unset the connection
        $iTableID = TTools::GetCMSTableId($this->table);
        $oTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->Init($iTableID, $this->id);
        $oTableEditor->SaveField($this->fieldName, $this->oFieldDefinition->sqlData['field_default_value']);
    }

    /**
     * callback method to remove mlt connections to deleted cms media files.
     */
    protected function _DelType_MULTITABLELIST()
    {
        // the image table is connected as an mlt... we need to remove any records in
        // the mlt corresponding to the image ID and the current record
        $iTableID = TTools::GetCMSTableId($this->table);
        $oTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oTableEditor->AllowDeleteByAll(true);
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->Init($iTableID, $this->id);
        $oTableEditor->RemoveMLTConnection($this->oFieldDefinition->sqlData['name'], $this->_fileID);
    }

    /**
     * callback method to remove cms media ids from image fields.
     */
    protected function _DelType_MEDIA()
    {
        $defaultImages = explode(',', $this->oFieldDefinition->sqlData['field_default_value']);
        $images = explode(',', $this->fieldVal);
        foreach ($images as $key => $imageID) {
            if ($imageID == $this->_fileID) {
                if (array_key_exists($key, $defaultImages)) {
                    $images[$key] = $defaultImages[$key];
                } else {
                    $images[$key] = '0';
                }
            }
        }
        $newValue = implode(',', $images);
        $iTableID = TTools::GetCMSTableId($this->table);
        $oTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->Init($iTableID, $this->id);
        $oTableEditor->SaveField($this->fieldName, $newValue);
    }

    /**
     * callback method to remove cms media image tags in wysiwyg fields.
     */
    protected function _DelType_TEXT()
    {
        $sImageIDEscaped = MySqlLegacySupport::getInstance()->real_escape_string($this->_fileID);
        $matchString = "/<img([^>]+?)cmsmedia=['\"]".$sImageIDEscaped."['\"](.*?)\\/>/usi";
        $newValue = preg_replace($matchString, '', $this->fieldVal);

        $iTableID = TTools::GetCMSTableId($this->table);
        $oTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->Init($iTableID, $this->id);
        $oTableEditor->SaveField($this->fieldName, $newValue);
    }
}
