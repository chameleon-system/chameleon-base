<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSDocumentLocalImport extends TCMSModelBase
{
    /**
     * tree node id where the files will be added to.
     *
     * @var string|null
     */
    protected $nodeID;

    /**
     * the import folder path.
     *
     * @var string|null
     */
    protected $sImportFolder;

    /**
     * the subdirectory which will be imported.
     *
     * @var string|null
     */
    protected $directory;

    /**
     * the table where the files will be imported to.
     *
     * @var string
     */
    protected $sTargetTable = 'cms_document';

    /**
     * the table of the tree where the imported files will be attached to.
     *
     * @var string
     */
    protected $sTargetTreeTable = 'cms_document_tree';

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        parent::Init();
        $this->nodeID = $this->global->GetUserData('nodeID');
        $this->data['nodeID'] = $this->nodeID;
        $this->sImportFolder = PATH_DOCUMENT_LOCAL_IMPORT_FOLDER;
    }

    /**
     * returns an array of the record data which will be saved via TableEditor.
     *
     * @param string $sFile
     *
     * @return array
     */
    protected function GetFileRecordData($sFile)
    {
        $realExtension = mb_substr($sFile, (mb_strrpos($sFile, '.') ? mb_strrpos($sFile, '.') + 1 : mb_strlen($sFile)), mb_strlen($sFile));
        $fileNameWithoutExtension = str_replace('.'.$realExtension, '', $sFile);
        $sNiceFileName = str_replace('_', ' ', $fileNameWithoutExtension);

        $postData = array();
        $postData['name'] = $sNiceFileName;
        $postData[$this->sTargetTreeTable.'_id'] = $this->nodeID;
        $postData['description'] = $sNiceFileName;
        $postData['private'] = $this->global->GetUserData('private');

        return $postData;
    }

    /**
     * this function should fill the data array and return a pointer to it
     * (pointer because it may contain objects).
     */
    public function &Execute()
    {
        parent::Execute();

        $message = $this->CheckLocalPath();
        if (true === $message) {
            $this->ShowDirectories();
        } else {
            $this->data['errorMessage'] = $message;
        }

        return $this->data;
    }

    /**
     * checks if import folder exists and is readable.
     *
     * @return mixed - returns true if folder is readable or error message
     */
    protected function CheckLocalPath()
    {
        $returnVal = true;

        if (!is_dir($this->sImportFolder)) {
            $returnVal = TGlobal::Translate('chameleon_system_core.document_local_import.error_path_not_found', array('%path%' => $this->sImportFolder));
        } else {
            if (!is_readable($this->sImportFolder)) {
                $returnVal = TGlobal::Translate('chameleon_system_core.document_local_import.error_no_read_access_to_path', array('%path%' => $this->sImportFolder));
            }
        }

        return $returnVal;
    }

    /**
     * loads directory structure for the select box.
     */
    protected function ShowDirectories()
    {
        $aScanlisting = scandir($this->sImportFolder);
        $dirlisting = array();
        $this->data['dirListing'] = $dirlisting;
        $bFilesInRootDirFound = false;
        $keyCount = 0;
        $iRootDirFilesCount = 0;
        foreach ($aScanlisting as $key => $file) {
            if (is_dir($this->sImportFolder.'/'.$file) && '.' != $file && '..' != $file && '.' != substr($file, 0, 1)) {
                ++$keyCount;
                $aScanlistingSubDir = scandir($this->sImportFolder.'/'.$file);
                $count = (count($aScanlistingSubDir) - 2);
                $aDir = array();
                $aDir['directory'] = $file;
                $aDir['filecount'] = $count;
                $this->data['dirListing'][$keyCount] = $aDir;
            } elseif (is_file($this->sImportFolder.'/'.$file) && '.' != $file && '..' != $file && '.' != substr($file, 0, 1)) {
                $bFilesInRootDirFound = true;
                ++$iRootDirFilesCount;
            }
        }

        if ($bFilesInRootDirFound) { // add base directory as a possible import directory
            $aDir['directory'] = 'base';
            $aDir['filecount'] = $iRootDirFilesCount;
            $this->data['dirListing'][0] = $aDir;
        }
    }

    /**
     * starts importing the files.
     */
    public function ImportFiles()
    {
        $this->directory = $this->global->GetUserData('directory');
        if ('/' == $this->directory) {
            $this->directory = '';
        }

        $subDir = $this->sImportFolder.'/'.$this->directory;
        $aScanlisting = scandir($subDir);
        if (is_array($aScanlisting) && count($aScanlisting) > 0) {
            foreach ($aScanlisting as $key => $file) {
                if (is_file($subDir.'/'.$file) && '.' != $file && '..' != $file && '.' != substr($file, 0, 1)) {
                    $this->ImportFile($file);
                }
            }

            // remove directory if it is not the basedir
            if ((!isset($this->data['fileErrors']) || 0 == count($this->data['fileErrors'])) && '' != $this->directory) {
                TTools::DelDir($subDir, true);
            }

            $this->ShowDirectories();
        }
    }

    /**
     * import a single file uisng table editor.
     *
     * @param string $sFile
     */
    protected function ImportFile($sFile)
    {
        $sTableID = TTools::GetCMSTableId($this->sTargetTable);
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->Init($sTableID, null);

        $aFileData = $this->GetFileData($sFile);
        $oTableEditor->oTableEditor->SetUploadData($aFileData, true);

        try {
            $aPostData = $this->GetFileRecordData($sFile);
            $oTableEditor->Save($aPostData);
            $this->data['importSuccess'][] = $this->directory.'/'.$sFile;

            if ('/' != substr($this->sImportFolder, -1)) {
                $this->sImportFolder = $this->sImportFolder.'/';
            }
        } catch (Exception $e) {
            $this->data['fileErrors'][] = $e->getMessage();
        }
    }

    /**
     * generates an array of meta data of the file in standard php upload format.
     *
     * @param string $sFile
     *
     * @return array
     */
    protected function GetFileData($sFile)
    {
        if ('/' !== substr($this->sImportFolder, -1)) {
            $this->sImportFolder = $this->sImportFolder.'/';
        }
        $subDir = $this->sImportFolder.$this->directory;
        if ('/' !== substr($subDir, -1)) {
            $subDir = $subDir.'/';
        }
        $sFilePath = $subDir.$sFile;

        $fileExtension = TTools::GetFileExtension($sFilePath);

        $oFileType = new TCMSRecord();
        $oFileType->table = 'cms_filetype';
        $oFileType->LoadFromField('file_extension', $fileExtension);

        $fileSize = filesize($sFilePath);

        $aFileData = array('name' => $sFile, 'type' => $oFileType->sqlData['content_type'], 'size' => $fileSize, 'tmp_name' => $sFilePath, 'error' => 0);

        return $aFileData;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'ImportFiles';
    }
}
