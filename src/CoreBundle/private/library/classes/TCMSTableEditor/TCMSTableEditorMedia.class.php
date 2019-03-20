<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\DeleteMediaEvent;

class TCMSTableEditorMedia extends TCMSTableEditorFiles
{
    /**
     * set public methods here that may be called from outside.
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'FetchConnections';
    }

    protected function GetTableName()
    {
        return 'cms_media';
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />'; // we need this for the list of usages
        return $aIncludes;
    }

    public function DatabaseCopy($languageCopy = false, $aOverloadedFields = array(), $bCopyAllLanguages = false)
    {
        $iFileId = $this->sId;
        $oImage = new TCMSImage();
        $oImage->Load($iFileId);
        $sFile = $oImage->GetFullLocalPath();
        parent::DatabaseCopy($languageCopy, $aOverloadedFields, $bCopyAllLanguages);
        $this->SaveField('path', $this->oTable->GetImageNameAsSeoName(), false);

        if (file_exists($sFile)) {
            $oImage = new TCMSImage();
            $oImage->Load($this->sId);
            $this->getFileManager()->copy($sFile, $oImage->GetFullLocalPath());
        }
    }

    protected function DataIsValid(&$postData, $oFields = null)
    {
        $isValid = parent::DataIsValid($postData, $oFields);

        if (!is_null($this->aUploadData)) {
            /*
            sent data:
            [Filedata] => Array
              (
                  [name] => 300_movie_article.jpg
                  [type] => application/octet-stream
                  [tmp_name] => /tmp/php7Sv9yk
                  [error] => 0
                  [size] => 77105
              )
            */

            if ($isValid) {
                $isValid = false;
                // Array of valid extensions
                $allowedFileTypes = $this->GetAllowedMediaTypes();
                if ($this->IsValidFileExtension($allowedFileTypes)) {
                    $isValid = $this->IsValidCMSMediaType($this->aUploadData['tmp_name'], $allowedFileTypes);
                }
            }

            $oConfig = &TdbCmsConfig::GetInstance();
            $imageUploadMaxSize = $oConfig->sqlData['max_image_upload_size'] * 1024;

            if ($isValid) {
                $isValid = $this->CheckMediaMaxProportions($this->aUploadData['tmp_name']);
                if ($isValid) {
                    if ($this->aUploadData['size'] <= $imageUploadMaxSize) {
                        $isValid = true;
                    } else {
                        throw new Exception(TGlobal::Translate('chameleon_system_core.table_editor_files.error_file_to_large',
                            array(
                                '%size%' => TCMSLocal::GetActive()->FormatNumber($this->aUploadData['size'], 0),
                                '%allowed%' => TCMSLocal::GetActive()->FormatNumber($imageUploadMaxSize, 0),
                            )
                        ), -240);
                    }
                }
            }
        }

        return $isValid;
    }

    /**
     * loads the allowed media types from TCMSImage or from external via
     * sAllowedFileTypes via GET/POST.
     *
     * @return array
     */
    public function GetAllowedMediaTypes()
    {
        $aAllowedFileTypes = TCMSImage::GetAllowedMediaTypes();

        $oGlobal = TGlobal::instance();
        /** @var $oGlobal TGlobal */
        $sAllowedFileTypesFromExternal = $oGlobal->GetUserData('sAllowedFileTypes'); // comma separated list of filetype endings e.g. jpg,gif

        // reduce allowed filetypes to external configured types
        $aAllowedFileTypesFinal = $aAllowedFileTypes;
        if (!empty($sAllowedFileTypesFromExternal)) {
            $aAllowedFileTypesFinal = array();
            $aAllowedFileTypesFromExternal = explode(',', $sAllowedFileTypesFromExternal);
            foreach ($aAllowedFileTypesFromExternal as $sFileType) {
                $sFileType = strtolower($sFileType);
                if (in_array($sFileType, $aAllowedFileTypes)) { // filetype allowed by system
                    $aAllowedFileTypesFinal[] = $sFileType;
                }
            }
        }

        return $aAllowedFileTypesFinal;
    }

    /**
     * checks media file width and height for max allowed proportions
     * or exact proportionas.
     *
     * @param string $sImagePath
     *
     * @return bool
     */
    protected function CheckMediaMaxProportions($sImagePath)
    {
        $isValid = true;

        $aMediaInfo = getimagesize($sImagePath);
        $iMediaWidth = $aMediaInfo[0];
        $iMediaHeight = $aMediaInfo[1];

        $oGlobal = TGlobal::instance();
        /** @var $oGlobal TGlobal */
        $bProportionExactMatch = $oGlobal->GetUserData('bProportionExactMatch');
        if (!empty($bProportionExactMatch)) {
            $bProportionExactMatch = true;
        }

        $iMaxUploadWidth = intval($oGlobal->GetUserData('iMaxUploadWidth'));
        if (!empty($iMaxUploadWidth)) {
            if (!$bProportionExactMatch && $iMediaWidth > $iMaxUploadWidth) {
                $isValid = false;
            } else {
                if ($bProportionExactMatch && $iMediaWidth != $iMaxUploadWidth) {
                    $isValid = false;
                }
            }
        }

        $iMaxUploadHeight = intval($oGlobal->GetUserData('iMaxUploadHeight'));
        if ($isValid) {
            if (!empty($iMaxUploadHeight)) {
                if (!$bProportionExactMatch && $iMediaHeight > $iMaxUploadHeight) {
                    $isValid = false;
                } else {
                    if ($bProportionExactMatch && $iMediaHeight != $iMaxUploadHeight) {
                        $isValid = false;
                    }
                }
            }
        }

        if (!$isValid) {
            if ($bProportionExactMatch) {
                throw new Exception(TGlobal::Translate('chameleon_system_core.table_editor_media.error_requires_exact_dimension',
                        array(
                            '%width%' => $iMediaWidth,
                            '%height%' => $iMediaHeight,
                            '%maxWidth%' => $iMaxUploadWidth,
                            '%maxHeight%' => $iMaxUploadHeight,
                        )
                        ), -240);
            } else {
                throw new Exception(TGlobal::Translate('chameleon_system_core.table_editor_media.error_invalid_dimensions',
                        array(
                            '%width%' => $iMediaWidth,
                            '%height%' => $iMediaHeight,
                            '%maxWidth%' => $iMaxUploadWidth,
                            '%maxHeight%' => $iMaxUploadHeight,
                        )), -240);
            }
        }

        return $isValid;
    }

    /**
     * checks uploaded file for right filetype.
     *
     * @param string $filePath
     * @param array  $allowedFileTypes
     *
     * @return bool
     */
    public function IsValidCMSMediaType($filePath, $allowedFileTypes)
    {
        $isValid = false;

        if (!is_null($this->aUploadData)) {
            if (isset($filePath) && !empty($filePath) && isset($allowedFileTypes) && is_array($allowedFileTypes)) {
                $imageInfo = false;
                // check if file extension and real filetype matches
                try {
                    $imageInfo = getimagesize($filePath);
                } catch (Exception $e) {
                    // file is no php supported image type
                }

                if (isset($imageInfo) && is_array($imageInfo)) {
                    $realFileExtension = image_type_to_extension($imageInfo[2]);
                    $realFileExtension = strtolower($realFileExtension);

                    $realFileExtension = str_replace('.', '', $realFileExtension);
                    if ('jpeg' == $realFileExtension || 'jpe' == $realFileExtension) {
                        $realFileExtension = 'jpg';
                    }

                    if (in_array($realFileExtension, $allowedFileTypes)) {
                        // check for CMYK images
                        if (isset($imageInfo['channels']) && 4 == $imageInfo['channels']) {
                            throw new Exception(TGlobal::Translate('chameleon_system_core.table_editor_media.error_cmyk'), -270);
                        // image is CMYK
                        } else {
                            $isValid = true;
                        }
                    } else {
                        throw new Exception(TGlobal::Translate('chameleon_system_core.table_editor_media.error_invalid_format'), -270);
                    }
                }
            }
        } else {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * returns the media pool base path (PATH_MEDIA_LIBRARY).
     *
     * @return string
     */
    protected function GetBaseTargetDirectory()
    {
        return PATH_MEDIA_LIBRARY;
    }

    /**
     * returns the full target image path without the image name.
     *
     * @return string
     */
    protected function GetTargetDirectory()
    {
        $sMediaPathString = '';
        if (!is_null($this->oTable)) {
            $sMediaPathString = $this->oTable->GetImageNameAsSeoName();
        }
        $sFullPath = $this->GetBaseTargetDirectory();
        if ('/' != substr($sFullPath, -1)) {
            $sFullPath .= '/';
        }
        $aParts = explode('/', $sMediaPathString);
        if (is_array($aParts) && count($aParts) > 1) {
            unset($aParts[count($aParts) - 1]);
            $sRelativePath = implode('/', $aParts);
            $sFullPath .= $sRelativePath;
        }

        return $sFullPath;
    }

    /**
     * here you can modify, clean or filter data before saving.
     *
     *
     * @var array $postData
     *
     * @return array
     */
    protected function PrepareDataForSave($postData)
    {
        $postData = parent::PrepareDataForSave($postData);

        if (!is_null($this->aUploadData)) {
            $fileExtension = TTools::GetFileExtension($this->aUploadData['name']);
            $fileExtensionLength = strlen($fileExtension);

            $fileNameWithoutExtension = substr($this->aUploadData['name'], 0, -($fileExtensionLength + 1));

            if (!isset($postData['description']) || empty($postData['description']) || ((isset($postData['description']) && isset($postData['uploadname'])) && $postData['description'] != $postData['uploadname'])) {
                if (!empty($postData['uploadname'])) {
                    $postData['description'] = $postData['uploadname'];
                } else {
                    if (empty($this->oTable->sqlData['description'])) {
                        $postData['description'] = str_replace('_', ' ', $fileNameWithoutExtension);
                    }
                }
            }
            if (!isset($postData['metatags']) || empty($postData['metatags'])) {
                if (!empty($postData['uploaddescription'])) {
                    $postData['metatags'] = $postData['uploaddescription'];
                } else {
                    if (empty($this->oTable->sqlData['metatags'])) {
                        $postData['metatags'] = str_replace('_', ' ', $fileNameWithoutExtension);
                    }
                }
            }

            $postData['custom_filename'] = $fileNameWithoutExtension;

            $imageInfo = getimagesize($this->aUploadData['tmp_name']);
            $mediaWidth = $imageInfo[0];
            $mediaHeight = $imageInfo[1];

            $postData['width'] = $mediaWidth;
            $postData['height'] = $mediaHeight;
        }

        return $postData;
    }

    /**
     * returns the tree fieldname.
     *
     * @return string
     */
    protected function GetTreeFieldName()
    {
        return 'cms_media_tree_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        if ($bReturnVal = parent::PostSaveHook($oFields, $oPostTable)) {
            if (array_key_exists('filesize', $oPostTable->sqlData)) {
                $this->SaveField('filesize', $oPostTable->sqlData['filesize']);
            }
            if (array_key_exists('height', $oPostTable->sqlData)) {
                $this->SaveField('height', $oPostTable->sqlData['height']);
            }
            if (array_key_exists('width', $oPostTable->sqlData)) {
                /*
                 * The width field is hidden and will therefore not be saved by default. We save it
                 * explicitly and also need to allow editing by all to bypass restrictions of the hidden state.
                 */
                // TODO remove with https://github.com/chameleon-system/chameleon-system/issues/220 - do not use TCMSFieldMediaProperties for "width"

                $this->AllowEditByAll(true);
                $this->SaveField('width', $oPostTable->sqlData['width']);
                $this->AllowEditByAll(false);
            }

            $this->SaveField('date_changed', date('Y-m-d H:i:s'));
            /**
             * @var TCmsMedia $media
             */
            $media = $this->oTable;
            $sNewName = $media->GetImageNameAsSeoName();
            $this->SaveField('path', $sNewName);

            if (!is_null($this->aUploadData)) {
                // clear thumbnails
                $oImage = new TCMSImage();
                $oImage->Load($this->sId);
                $oImage->ClearThumbnails();
            } else { // no new image uploaded
                // check if we need to rename the object
                if (is_object($this->oTablePreChangeData) && '' === $media->sqlData['external_video_id']) {
                    $sOldName = $this->oTablePreChangeData->fieldPath;

                    if ($sOldName !== $sNewName) {
                        $sourcePath = PATH_MEDIA_LIBRARY.'/'.$sOldName;
                        $targetPath = PATH_MEDIA_LIBRARY.'/'.$sNewName;
                        if (true === \is_readable($sourcePath) && true === \is_file($sourcePath)) {
                            $this->CreateSubPathInImagePath();
                            $this->getFileManager()->move($sourcePath, $targetPath);
                        }
                    }
                }
            }
            // if there was a change of the image, we need to clear related cache elements
            if (is_object($this->oTablePreChangeData)) {
                $this->ClearCacheOfObjectsUsingImage($media->id);
            }
        }

        return $bReturnVal;
    }

    /**
     * Refreshes the preview image to viddler. If no preview image was set,
     * then get preview image from video starting point.
     *
     * @deprecated since 6.2.0 - Viddler is no longer supported
     */
    protected function RefreshImageOnViddler()
    {
    }

    /**
     * creates the sub dirs based on media pool base directory and image SEO path.
     */
    protected function CreateSubPathInImagePath()
    {
        $sFullPath = $this->GetTargetDirectory();
        if (!is_dir($sFullPath) && !file_exists($sFullPath)) {
            if (!$this->getFileManager()->mkdir($sFullPath, 0777, true)) {
                TTools::WriteLogEntry('could not create image subdir: '.$sFullPath, 1, __FILE__, __LINE__);
            }
        }
    }

    /**
     * @deprecated since 6.2.0 - use chameleon_system_media_manager.usages.finder_collection from media manager bundle
     * to find usages
     *
     * returns an array of _TCMSMediaConnections holding all fields with matching values in all
     * tables that contain the image given by the image id $fileID
     *
     * @param string $fileID - an image id (from the table cms_media)
     *
     * @return TCMSMediaConnections[]
     */
    public function FetchConnections($fileID, $aTableBlackList = null)
    {
        $aMediaConnections = array();
        // get all table confs that contain fields that may hold images
        $oImageTables = new TListImageTables();
        /** @var $oImageTables TListImageTables */

        // get a list of field types that can hold images
        $imageFieldTypes = TCMSFieldDefinition::GetImageFieldTypes();

        $tmpList = array(); // an array in which we collect the fields from the table configs that may
        // hold images (the original configs hold all fields, not just the once that
        // may contain images

        // loop through all image tables
        while ($oImageTable = $oImageTables->Next()) {
            /** @var $oImageTable TCMSTableConf */
            if (!empty($aTableBlackList) && in_array($oImageTable->sqlData['name'], $aTableBlackList)) {
                continue;
            }
            // get an iterator of image fields within the current table
            $oImageFields = &$oImageTable->GetFieldDefinitions($imageFieldTypes);

            $tmpList[$oImageTable->GetName()] = array();

            // loop through the image fields and store them in our tempList
            while ($oImageField = $oImageFields->Next()) {
                /** @var $oImageField TCMSFieldDefinition */
                $tmpList[$oImageTable->GetName()][$oImageField->GetName()] = $oImageField;
            }

            // now find all records in the current table that actually contain the image
            $oMatchingRecords = self::_GetImageRecords($fileID, $oImageTable, $tmpList[$oImageTable->GetName()]);

            // now for each record create as many TCMSMediaConnection objects as we have matching fields in that table
            while ($oMatchingRecord = &$oMatchingRecords->Next()) {
                /** @var $oMatchingRecord TCMSRecord */
                $oImageFields->GoToStart();
                // for the record that matches, include only the fields that caused that match
                while ($oImageField = &$oImageFields->Next()) {
                    /** @var $oImageField TCMSFieldDefinition */
                    if (self::_FieldContainsImage($fileID, $oMatchingRecord, $oImageField)) {
                        $aMediaConnections[] = self::_GetMediaConnectionObject($oMatchingRecord, $oImageTable, $oImageField);
                    }
                }
            }
        }

        return $aMediaConnections;
    }

    /**
     * @deprecated since 6.2.0 - use chameleon_system_media_manager.usages.finder_collection from media manager bundle
     * to find usages
     *
     * returns true if the image identified through sImageID is in the field oImageField
     * within the record oTable
     *
     * @param string              $sImageID    - id of the image (from table "images")
     * @param TCMSRecord          $oTable      - record being inspected
     * @param TCMSFieldDefinition $oImageField - field being inspected
     *
     * @return bool
     */
    public static function _FieldContainsImage($sImageID, &$oTable, &$oImageField)
    {
        $bFieldContainsImage = false;
        $oFieldType = $oImageField->GetFieldType();
        $content = ''; //mlt fields
        if (isset($oTable->sqlData[$oImageField->sqlData['name']])) {
            $content = $oTable->sqlData[$oImageField->sqlData['name']];
        }

        switch ($oFieldType->sqlData['constname']) {
            case 'CMSFIELD_TABLELIST':
                // tablelist is only relevant if it connects to images (ie its name is images_id)
                if ('cms_media_id' == $oImageField->sqlData['name']) {
                    if ($content == $sImageID) {
                        $bFieldContainsImage = true;
                    }
                }
                break;
            case 'CMSFIELD_MULTITABLELIST':
                if ('cms_media_mlt' == $oImageField->sqlData['name']) {
                    $oTableConfig = &$oTable->GetTableConf();
                    $smltTable = $oTableConfig->sqlData['name'].'_'.$oImageField->sqlData['name'];
                    $query = 'SELECT *
      	                FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($smltTable)."`
      	               WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTable->id)."'
      	                 AND `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sImageID)."'
      	             ";
                    if (MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                        $bFieldContainsImage = true;
                    }
                }
                break;
            case 'CMSFIELD_MEDIA':
            case 'CMSFIELD_EXTENDEDTABLELIST_MEDIA':
                $imageIds = explode(',', $content);
                if (in_array($sImageID, $imageIds)) {
                    $bFieldContainsImage = true;
                }
                break;
            case 'CMSFIELD_WYSIWYG':
            case 'CMSFIELD_WYSIWYG_LIGHT':
                if ((false !== strpos($content, 'cmsmedia="'.MySqlLegacySupport::getInstance()->real_escape_string($sImageID).'"'))) {
                    $bFieldContainsImage = true;
                }
                break;
            default:
                trigger_error('Error in $oImageField. Field '.TGlobal::OutHTML($oFieldType->sqlData['constname']).' is not able to hold images', E_USER_ERROR);
                break;
        }

        return $bFieldContainsImage;
    }

    /**
     * @deprecated since 6.2.0 - use chameleon_system_media_manager.usages.finder_collection from media manager bundle
     * to find usages
     *
     * returns a RecordList object of all records in a table that hold the given image
     *
     * @param string        $sImageID        - id of the image we are looking for
     * @param TCMSTableConf $oImageTable     - the definition of the Table we currently searching in
     * @param array         $aImageFieldList - array of TCMSFieldDefinition of the fields within the table that are capable of holding images
     *
     * @return TCMSRecordList
     */
    public static function _GetImageRecords($sImageID, $oImageTable, $aImageFieldList)
    {
        $query = 'SELECT DISTINCT `'.MySqlLegacySupport::getInstance()->real_escape_string($oImageTable->sqlData['name']).'`.*
                  FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($oImageTable->sqlData['name']).'`
               ';
        $fieldQuery = '';
        $sMLTTables = '';
        $sImageIDEscaped = MySqlLegacySupport::getInstance()->real_escape_string($sImageID);
        foreach ($aImageFieldList as $fieldName => $oImageField) {
            /** @var $oImageField TCMSFieldDefinition */
            $oFieldType = $oImageField->GetFieldType();
            $sFName = MySqlLegacySupport::getInstance()->real_escape_string($oImageField->sqlData['name']);
            switch ($oFieldType->sqlData['constname']) {
                case 'CMSFIELD_TABLELIST':
                    // tablelist is only relevant if it connects to images (ie its name is images_id)
                    if ('cms_media_id' == $oImageField->sqlData['name']) {
                        if (!empty($fieldQuery)) {
                            $fieldQuery .= ' OR ';
                        }
                        $fieldQuery .= " (`{$sFName}` = '{$sImageIDEscaped}') ";
                    }
                    break;
                case 'CMSFIELD_MULTITABLELIST':
                    if ('cms_media_mlt' == $oImageField->sqlData['name']) {
                        $smltTable = $oImageTable->sqlData['name'].'_'.$oImageField->sqlData['name'];
                        $sMLTTables .= " LEFT JOIN {$smltTable} AS {$smltTable} ON `".MySqlLegacySupport::getInstance()->real_escape_string($oImageTable->sqlData['name'])."`.`id` = {$smltTable}.`source_id`";
                        if (!empty($fieldQuery)) {
                            $fieldQuery .= ' OR ';
                        }
                        $fieldQuery .= " ({$smltTable}.`target_id` = '{$sImageIDEscaped}') ";
                    }
                    break;
                case 'CMSFIELD_MEDIA':
                case 'CMSFIELD_EXTENDEDTABLELIST_MEDIA':
                    if (!empty($fieldQuery)) {
                        $fieldQuery .= ' OR ';
                    }
                    $fieldQuery .= "
        	   (`{$sFName}` LIKE '%,{$sImageIDEscaped}' OR
        	    `{$sFName}` LIKE '{$sImageIDEscaped},%' OR
        	    `{$sFName}` LIKE '%,{$sImageIDEscaped},%' OR
        	    `{$sFName}` = '{$sImageIDEscaped}')
        	  ";
                    break;
                case 'CMSFIELD_WYSIWYG':
                case 'CMSFIELD_WYSIWYG_LIGHT':
                    if (!empty($fieldQuery)) {
                        $fieldQuery .= ' OR ';
                    }
                    $fieldQuery .= " (`{$sFName}` LIKE '%cmsmedia=\"".MySqlLegacySupport::getInstance()->real_escape_string($sImageIDEscaped)."\"%') ";
                    break;
                default:
                    trigger_error('Error getting ImageRecord. Field '.TGlobal::OutHTML($oFieldType->sqlData['constname']).' is not able to hold images', E_USER_ERROR);
                    break;
            }
        }

        if (!empty($fieldQuery)) {
            $query .= $sMLTTables.' WHERE '.$fieldQuery;
        } else {
            $query .= $sMLTTables.' WHERE 1=2';
        }

        $sClassName = TCMSTableToClass::GetClassName('Tdb', $oImageTable->sqlData['name']).'List';
        if (!class_exists($sClassName, false)) {
        } // when calling the method via index.php the autoload will sometimes fail (no idea why). this fixes the problem.
        $oImageRecords = &call_user_func_array(array($sClassName, 'GetList'), array($query, null, false, true, true));

        return $oImageRecords;
    }

    /**
     * @deprecated since 6.2.0 - use chameleon_system_media_manager.usages.finder_collection from media manager bundle
     * to find usages
     *
     * factory generating an instance of the _TCMSMediaConnections. The object
     * will be initialised with the data from oTable, oTableConf, and oFieldDefinition
     *
     * @param TCMSRecord          $oTable           - record that holds the image
     * @param TCMSTableConf       $oTableConf       - definition of oTable
     * @param TCMSFieldDefinition $oFieldDefinition - the field that holds the image
     *
     * @return TCMSMediaConnections
     */
    public static function &_GetMediaConnectionObject(&$oTable, &$oTableConf, &$oFieldDefinition)
    {
        $oMediaConnection = new TCMSMediaConnections();
        $oMediaConnection->table = $oTable->table;
        $oMediaConnection->tableName = $oTableConf->GetName();
        $oMediaConnection->id = $oTable->id;
        $oMediaConnection->fieldName = $oFieldDefinition->sqlData['name'];
        $oMediaConnection->fieldVal = $oTable->sqlData[$oMediaConnection->fieldName];
        $oMediaConnection->recordName = $oTable->GetName();
        $oMediaConnection->fieldTranslationName = $oFieldDefinition->sqlData['translation'];
        $oMediaConnection->oFieldDefinition = $oFieldDefinition;

        return $oMediaConnection;
    }

    /**
     * Delete the image with fileID. all connection in all tables will be removed as well.
     *
     * @param string $fileID - the image id (from the table images)
     */
    public function Delete($fileID = null)
    {
        $this->getEventDispatcher()->dispatch(CoreEvents::BEFORE_DELETE_MEDIA, new DeleteMediaEvent($fileID));
        $this->deleteExternalMediaFile($fileID);
        parent::Delete($fileID);
    }

    /**
     * triggers API call to delete external video file.
     *
     * @param string $fileID
     */
    protected function deleteExternalMediaFile($fileID)
    {
    }

    /**
     * call the delete method without removing ids connected to the image. use this ONLY if you are sure, that there
     * are no other connections.
     *
     * @param  $fileID
     */
    public function DeleteWithoutRemovingOldConnections($fileID)
    {
        parent::Delete($fileID);
    }

    /**
     * is called only from Delete method and calls all delete relevant methods
     * executes the final SQL Delete Query.
     *
     * @return bool
     */
    protected function DeleteExecute()
    {
        $bDeleteSuccess = false;
        if ($this->DeleteFile($this->oTable->id)) {
            $bDeleteSuccess = parent::DeleteExecute();
        }

        return $bDeleteSuccess;
    }

    /**
     * Delete the image with fileID.
     *
     * @param string $fileID - the image id (from the table images)
     *
     * @return bool
     */
    protected function DeleteFile($fileID)
    {
        $bDeleteSuccess = true;
        $oImage = new TCMSImage();
        $oImage->Load($fileID);

        $imagePath = $oImage->GetFullLocalPath();
        if (file_exists($imagePath) && !is_dir($imagePath)) {
            $bDeleteSuccess = $this->getFileManager()->delete($imagePath);
            $oImage->ClearThumbnails();
        }

        return $bDeleteSuccess;
    }

    /**
     * copy uploaded workflow temp image to mediapool.
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    protected function MoveWorkflowImageToMediaPool()
    {
    }

    /**
     * get the target file name with directory.
     *
     * @return string
     */
    protected function GetTargetFileName()
    {
        $sTargetDir = $this->GetBaseTargetDirectory();
        $sTargetFile = $sTargetDir.'/'.$this->oTable->GetImageNameAsSeoName();

        return $sTargetFile;
    }

    /**
     * trys to move an uploaded file to target directory
     * if the move failes it deletes the database record.
     */
    protected function MoveFile($sourceFile, $targetFile, $treatAsUploadedFile)
    {
        $this->CreateSubPathInImagePath();

        parent::MoveFile($sourceFile, $targetFile, $treatAsUploadedFile);
    }

    /**
     * @param string $sImageId
     */
    public function ClearCacheOfObjectsUsingImage($sImageId)
    {
        if (true === TCacheManager::IsCachingEnabled()) {
            $aData = $this->FetchConnections($sImageId);
            if (is_array($aData)) {
                /** @var $oConnection TCMSMediaConnections */
                foreach ($aData as $oConnection) {
                    TCacheManager::PerformeTableChange($oConnection->tableName, $oConnection->id);
                }
            }
        }
    }
}
