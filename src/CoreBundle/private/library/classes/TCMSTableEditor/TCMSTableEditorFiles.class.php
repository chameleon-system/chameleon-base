<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Component\Filesystem\Filesystem;

class TCMSTableEditorFiles extends TCMSTableEditor
{
    /**
     *  upload meta data in format of $_FILES
     *  array('name'=>'testfile.jpg','type'=>'application/octet-stream','tmp_name'=>'/tmp/php7Sv9yk','error'=>0,'size'=>77105).
     *
     * @var array
     */
    protected $aUploadData;

    /**
     * prevents is_uploaded_file() check
     * set this to true if you want to import files from a local path.
     *
     * @var bool
     */
    protected $bAllowSaveOfNotUploadedFiles = false;

    /**
     *  allows the use of the table editor for files coming from local files e.g. ZIP.
     *
     *  $aData needs to be in format of $_FILES upload data
     *  array('name'=>'testfile.jpg','type'=>'application/octet-stream','tmp_name'=>'/tmp/php7Sv9yk','error'=>0,'size'=>77105)
     *
     * @param array $aData
     * @param bool $bAllowSaveOfNotUploadedFiles - set to true if the upload data is not from an upload (for example when the images come from a zip)
     */
    public function SetUploadData($aData, $bAllowSaveOfNotUploadedFiles = false)
    {
        $this->aUploadData = $aData;
        $this->bAllowSaveOfNotUploadedFiles = $bAllowSaveOfNotUploadedFiles;
    }

    public function Init($tableid = null, $id = null, $sLanguageID = null)
    {
        if (is_null($tableid)) {
            // get table id of cms_media
            $oTableConf = new TCMSRecord();
            /* @var $oTableConf TCMSRecord */
            $oTableConf->table = 'cms_tbl_conf';
            $oTableConf->LoadFromField('name', $this->GetTableName());
            $tableid = $oTableConf->sqlData['id'];
        }

        parent::Init($tableid, $id, $sLanguageID);
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
                if (!is_null($this->aUploadData)) {
                    if (0 == $this->aUploadData['error'] && (is_uploaded_file($this->aUploadData['tmp_name']) || $this->bAllowSaveOfNotUploadedFiles)) {
                        $maxSize = TTools::GetUploadMaxSizeBytes();
                        if ($this->aUploadData['size'] <= $maxSize || $this->bAllowSaveOfNotUploadedFiles) { // ignore upload max size on local files
                            $isValid = true;
                        }
                    } else {
                        throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_universal_uploader.error_upload').': '.$this->aUploadData['error'], -250);
                    }
                }
            }
        }

        return $isValid;
    }

    /**
     * checks if the file extension is supported by the CMS
     * and is allowed to be uploaded.
     *
     * @param array $allowedFileTypes
     *
     * @return bool
     */
    protected function IsValidFileExtension($allowedFileTypes)
    {
        $isValid = false;
        if (!is_null($this->aUploadData)) {
            $fileExtension = TTools::GetFileExtension($this->aUploadData['name']);
            if (in_array($fileExtension, $allowedFileTypes)) {
                $isValid = true;
            } else {
                $oGlobal = TGlobal::instance();
                /** @var $oGlobal TGlobal */
                $sAllowedFileTypesFromExternal = $oGlobal->GetUserData('sAllowedFileTypes'); // comma separated list of filetype endings e.g. jpg,gif
                if (!empty($sAllowedFileTypesFromExternal)) {
                    $sAllowedFileTypesFromExternal = strtoupper($sAllowedFileTypesFromExternal);
                    $sAllowedFileTypesFromExternal = str_replace(',', ', ', $sAllowedFileTypesFromExternal);
                    throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_universal_uploader.error_type_not_allowed', ['%sFileType%' => strtoupper($fileExtension), 'sAllowedFileTypesFromExternal' => $sAllowedFileTypesFromExternal]), -270);
                } else {
                    throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_universal_uploader.error_type_not_supported').': '.strtoupper($fileExtension), -270);
                }
            }
        } else {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator $oFields holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     *
     * @return bool
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $bMoveSuccess = true;
        if (!is_null($this->aUploadData)) {
            try {
                $this->MoveFile(
                    $this->aUploadData['tmp_name'],
                    $this->GetTargetFileName(),
                    false === $this->bAllowSaveOfNotUploadedFiles
                );
            } catch (Exception $e) {
                // This cleanup is problematic since it fails on updates and the file may have made it to some of the servers.
                // We should use a transaction to prevent db changes on failure and we need a way to revert the image copy if we are overwriting an existing file
                // and run into problems (on one or more servers). We'll leave that for later though since the existing code did not handle this case either
                if (!$this->bIsUpdateCall) {
                    $this->Delete($this->sId);
                }

                throw $e;
            }
        }

        return $bMoveSuccess;
    }

    /**
     * returns the media/document save path
     * method needs to be overwritten.
     *
     * @return string
     */
    protected function GetTargetDirectory()
    {
        return '/tmp';
    }

    /**
     * get the target file name with directory.
     *
     * @return string
     */
    protected function GetTargetFileName()
    {
        $sTargetDir = $this->GetTargetDirectory();
        $sTargetFile = $sTargetDir.'/'.$this->sId.'.'.TTools::GetFileExtension($this->aUploadData['name']);

        return $sTargetFile;
    }

    /**
     * trys to move an uploaded file to target directory
     * if the move failes it deletes the database record.
     *
     * @param string $sourceFile full path to source file
     * @param string $targetFile full path to target file
     * @param bool $treatAsUploadedFile will use move_uploaded_file to move the file
     *
     * @throws Exception
     */
    protected function MoveFile($sourceFile, $targetFile, $treatAsUploadedFile)
    {
        if (null === $this->sId) {
            return; // no idea how this could ever happen - I would think this should throw an exception, but I don't want to change behaviour here
        }

        $targetDir = dirname($targetFile);

        if (false === is_dir($targetDir)) {
            throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_file.error_target_folder_missing').' '.$targetDir, -220);
        }

        if (false === is_writable($targetDir)) {
            throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_file.error_missing_write_permission_on_target_folder').' '.$targetDir, -220);
        }

        // if the file exists (we are overwriting it) then we should also have write permission
        if (true === file_exists($targetFile) && false === is_writable($targetFile)) {
            throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_files.error_missing_permission_to_overwrite_existing_file').' '.$targetFile, -220);
        }

        if (true === $treatAsUploadedFile) {
            if (false === move_uploaded_file($sourceFile, $targetFile)) {
                throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_files.error_expected_uploaded_file').' '.$sourceFile, -220);
            }
        } else {
            if (true === file_exists($targetFile)) {
                // rename mail fail to overwrite the target - even though php.net claims that it will. at least when using ACL permissions on the server. so solve this we remove the target file if it exists
                unlink($targetFile);
            }
            if (false === rename($sourceFile, $targetFile)) {
                // in this case the source file could not be moved. the original code would not fail here, but try a copy. again, we want to keep the behavior for now... :-/
                if (false === copy($sourceFile, $targetFile)) {
                    throw new Exception(ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_files.error_unable_to_move_or_copy_file').' '.$targetFile, -220);
                }
            }
        }
    }

    protected function PrepareDataForSave($postData)
    {
        $postData = parent::PrepareDataForSave($postData);

        if (!is_null($this->aUploadData)) {
            $fileExtension = TTools::GetFileExtension($this->aUploadData['name']);
            $fileExtensionLength = strlen($fileExtension);

            $fileNameWithoutExtension = substr($this->aUploadData['name'], 0, -($fileExtensionLength + 1));

            if (!isset($postData['name']) || empty($postData['name'])) {
                if (!empty($postData['uploadname'])) {
                    $postData['name'] = $postData['uploadname'];
                } else {
                    if (empty($this->oTable->sqlData['name'])) {
                        $postData['name'] = str_replace('_', ' ', $fileNameWithoutExtension);
                    }
                }
            }
            if (!isset($postData['description']) || empty($postData['description'])) {
                if (!empty($postData['uploaddescription'])) {
                    $postData['description'] = $postData['uploaddescription'];
                } else {
                    if (empty($this->oTable->sqlData['description'])) {
                        $postData['description'] = str_replace('_', ' ', $fileNameWithoutExtension);
                    }
                }
            }
            if (!isset($postData['filesize']) || $postData['filesize'] != $this->aUploadData['size']) {
                $postData['filesize'] = $this->aUploadData['size'];
            }

            if ($fileTypeID = TCMSDownloadFile::GetFileTypeIdByExtension($fileExtension)) { // works for media types, too
                $postData['cms_filetype_id'] = $fileTypeID;
            }

            $oGlobal = TGlobal::instance();
            /** @var $oGlobal TGlobal */
            if ($oGlobal->UserDataExists('treeNodeID')) {
                $treeFieldName = $this->GetTreeFieldName();
                if (!isset($postData[$treeFieldName]) || empty($postData[$treeFieldName])) {
                    $postData[$treeFieldName] = $oGlobal->GetUserData('treeNodeID');
                }
            }

            $postData['time_stamp'] = date('y-m-d H:i:s', time());
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            $userID = $securityHelper->getUser()?->getId();
            $postData['cms_user_id'] = $userID;
        }

        return $postData;
    }

    /**
     * allows subclasses to overwrite default values.
     *
     * @param TIterator $oFields
     */
    protected function PrepareFieldsForSave($oFields)
    {
        parent::PrepareFieldsForSave($oFields);

        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            /** @var $oField TCMSField */
            if ('cms_user_id' != $oField->name) {
                $oField->oDefinition->sqlData['modifier'] = 'none';
            }
        }
        $oFields->GoToStart();
    }

    /**
     * returns an iterator with the menuitems for the current table. if you want to add your own
     * items, overwrite the GetCustomMenuItems (NOT GetMenuItems)
     * the iterator will always be reset to start.
     *
     * @return TIterator
     */
    public function GetMenuItems()
    {
        parent::GetMenuItems();

        $this->oMenuItems->RemoveItem('sItemKey', 'new');
        $this->oMenuItems->RemoveItem('sItemKey', 'delete');
        $this->oMenuItems->RemoveItem('sItemKey', 'copy');
        $this->oMenuItems->RemoveItem('sItemKey', 'edittableconf');

        return $this->oMenuItems;
    }

    protected function getFileManager(): Filesystem
    {
        return new Filesystem();
    }
}
