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
use ChameleonSystem\CoreBundle\ServiceLocator;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class TCMSTableEditorMedia extends TCMSTableEditorFiles
{
    protected function GetTableName()
    {
        return 'cms_media';
    }

    public function DatabaseCopy($languageCopy = false, $aOverloadedFields = [], $bCopyAllLanguages = false)
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

    protected function DataIsValid($postData, $oFields = null)
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

            $oConfig = TdbCmsConfig::GetInstance();
            $imageUploadMaxSize = $oConfig->sqlData['max_image_upload_size'] * 1024;

            if ($isValid) {
                $isValid = $this->CheckMediaMaxProportions($this->aUploadData['tmp_name']);
                if ($isValid) {
                    if ($this->aUploadData['size'] <= $imageUploadMaxSize) {
                        $isValid = true;
                    } else {
                        throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_files.error_file_to_large',
                            [
                                '%size%' => TCMSLocal::GetActive()->FormatNumber($this->aUploadData['size'], 0),
                                '%allowed%' => TCMSLocal::GetActive()->FormatNumber($imageUploadMaxSize, 0),
                            ]
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
            $aAllowedFileTypesFinal = [];
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
                throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_media.error_requires_exact_dimension',
                    [
                        '%width%' => $iMediaWidth,
                        '%height%' => $iMediaHeight,
                        '%maxWidth%' => $iMaxUploadWidth,
                        '%maxHeight%' => $iMaxUploadHeight,
                    ]
                ), -240);
            } else {
                throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_media.error_invalid_dimensions',
                    [
                        '%width%' => $iMediaWidth,
                        '%height%' => $iMediaHeight,
                        '%maxWidth%' => $iMaxUploadWidth,
                        '%maxHeight%' => $iMaxUploadHeight,
                    ]), -240);
            }
        }

        return $isValid;
    }

    /**
     * checks uploaded file for right filetype.
     *
     * @param string $filePath
     * @param array $allowedFileTypes
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
                            throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_media.error_cmyk'), -270);
                        // image is CMYK
                        } else {
                            $isValid = true;
                        }
                    } else {
                        throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_media.error_invalid_format'), -270);
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
     * @var array
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
    protected function PostSaveHook($oFields, $oPostTable)
    {
        if ($bReturnVal = parent::PostSaveHook($oFields, $oPostTable)) {
            $isUpdate = $this->bIsUpdateCall; // the bIsUpdateCall will be falsely set to true in all SaveField methods here

            if (array_key_exists('filesize', $oPostTable->sqlData)) {
                $this->SaveField('filesize', $oPostTable->sqlData['filesize']);
            }
            if (array_key_exists('height', $oPostTable->sqlData)) {
                $this->SaveField('height', $oPostTable->sqlData['height']);
            }

            $sOldName = $this->oTablePreChangeData->fieldPath ?? '';
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
                    if ($sOldName !== $sNewName && '' !== $sOldName) {
                        $sourcePath = PATH_MEDIA_LIBRARY.'/'.$sOldName;
                        $targetPath = PATH_MEDIA_LIBRARY.'/'.$sNewName;
                        if (true === \is_readable($sourcePath) && true === \is_file($sourcePath)) {
                            $this->CreateSubPathInImagePath();
                            $this->getFileManager()->rename($sourcePath, $targetPath, true);
                        }
                    }
                }
            }

            // if there was a change of the image, we need to clear related cache elements
            if (true === $isUpdate) {
                $this->ClearCacheOfObjectsUsingImage($media->id);
            }
        }

        return $bReturnVal;
    }

    /**
     * creates the sub dirs based on media pool base directory and image SEO path.
     */
    protected function CreateSubPathInImagePath()
    {
        $sFullPath = $this->GetTargetDirectory();
        if (!is_dir($sFullPath) && !file_exists($sFullPath)) {
            try {
                $this->getFileManager()->mkdir($sFullPath);
            } catch (IOExceptionInterface $exception) {
                TTools::WriteLogEntry('could not create image subdir: '.$sFullPath, 1, __FILE__, __LINE__);
            }
        }
    }

    /**
     * Delete the image with fileID. all connection in all tables will be removed as well.
     *
     * @param string $fileID - the image id (from the table images)
     */
    public function Delete($fileID = null)
    {
        $this->getEventDispatcher()->dispatch(new DeleteMediaEvent($fileID), CoreEvents::BEFORE_DELETE_MEDIA);
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
     * @param string $fileID
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
            try {
                $this->getFileManager()->remove($imagePath);
            } catch (IOExceptionInterface $exception) {
                $bDeleteSuccess = false;
            }

            $oImage->ClearThumbnails();
        }

        return $bDeleteSuccess;
    }

    /**
     * get the target file name with directory.
     *
     * @return string
     */
    protected function GetTargetFileName()
    {
        $sTargetDir = $this->GetBaseTargetDirectory();

        return $sTargetDir.'/'.$this->oTable->GetImageNameAsSeoName();
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
        /*
         * This method is replaced by its counterpart in the subclass
         * \ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\TableEditor\CmsMediaTableEditor
         *
         * If this method is called instead of CmsMediaTableEditor, be sure to set the sub-class as media table editor
         * (see update 1517924152 in MediaManagerBundle).
         *
         * This constellation is from a time where the "new" media manager was optional and is now only kept for
         * simplicity. Ideas for improvements:
         * - Move the code from CmsMediaTableEditor. This wasn't done yet as it would bring in a hard dependency to
         *   MediaManagerBundle. We already have such dependencies, but should avoid them because MediaManagerBundle
         *   already depends on CoreBundle so this would mean cyclic dependency between components, which is evil.
         * - Dispatch an event on image change. I didn't do this right away because it's unclear exactly when to
         *   dispatch. The caller of this method implies it should only be dispatched on significant changes, but in
         *   reality it is called on every save. This should be investigated first.
         */
    }

    private function getCache(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }
}
