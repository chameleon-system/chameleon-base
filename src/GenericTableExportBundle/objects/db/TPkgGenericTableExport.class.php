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

class TPkgGenericTableExport extends TPkgGenericTableExportAutoParent
{
    // export path after CustomerData path (cmsdata)
    /**
     * @var string
     */
    protected $sExportPath = 'export';

    /**
     * Fallback method for older clients.
     *
     * @param null $sId
     * @param bool $bUtf8Decode
     *
     * @return mixed
     */
    public function WriteExport($sId = null, $bUtf8Decode = false)
    {
        return self::WriteExportToFile($sId, $bUtf8Decode);
    }

    /**
     * Write a generated export to file base.
     *
     * sId defines the record to export or null to export whole list
     *
     * @param string|null $sId
     * @param bool        $bUtf8Decode
     *
     * @return bool $bSuccess
     */
    public function WriteExportToFile($sId = null, $bUtf8Decode = false)
    {
        $bSuccess = false;
        $sOutput = $this->GetExport($sId);
        if ($bUtf8Decode) {
            $sOutput = utf8_decode($sOutput);
        }

        $this->createExportDirectoryIfNeeded();

        $exportFilePath = $this->getExportFilePath($sId);

        // please please please don't do it like that. This is a very very rare moment, where it is necessary due to missing infrastructure.
        /** @var IPkgCmsFileManager $fileManager */
        $fileManager = ServiceLocator::get('chameleon_system_core.filemanager');

        if ($pFile = $fileManager->fopen($exportFilePath, 'wb')) {
            if ($fileManager->fwrite($pFile, $sOutput)) {
                $bSuccess = true;
            }
            $fileManager->fclose($pFile);
        } else {
            trigger_error("Can't open '{$exportFilePath}' for writing - check directory and rights", E_USER_WARNING);
        }

        return $bSuccess;
    }

    /**
     * @param null|string $sId
     *
     * @return string
     */
    public function getExportFilePath($sId = null)
    {
        $sFileName = $this->GetFileName($sId);
        $sPath = $this->getExportDirectoryPath();

        return $sPath.$sFileName;
    }

    /**
     * Offer a generated export for download.
     *
     * sId defines the record to export or null to export whole list
     *
     * @param string|null $sId
     * @param bool        $bUtf8Decode
     *
     * @return never
     */
    public function WriteExportToDownload($sId = null, $bUtf8Decode = false)
    {
        $sOutput = $this->GetExport($sId);
        if ($bUtf8Decode) {
            $sOutput = utf8_decode($sOutput);
        }
        $sFileName = self::GetFileName($sId);
        $sContentType = self::GetContentType($sFileName);
        if (false === $sContentType) {
            $sContentType = 'text/plain';
        }

        header('Pragma: public'); // required
        header('Expires: 0'); // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: '.$sContentType);
        header('Content-Disposition: attachment; filename="'.$sFileName.'"');
//        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.strlen($sOutput)); // provide file size
        echo $sOutput;
        exit(0);
    }

    /**
     * Builds the file name for the current export, based on the configuration given by the user.
     *
     * @param null|string $sId
     *
     * @return string
     */
    private function GetFileName($sId)
    {
        $name = $sId;
        if (is_null($name)) {
            $name = 'list';
        }
        $sFileName = $name.'-'.TCMSUserInput::FilterValue($this->fieldExportFilename, TCMSUserInput::FILTER_FILENAME);

        return $sFileName;
    }

    /**
     * returns the content type that maps to the file extension of the given file name
     * or false if file type could not be found in cms database.
     *
     * @param string $fileExtension
     * @param string $sFileName
     *
     * @return int - id of file type... false if file type wasn't found
     */
    public static function GetContentType($sFileName)
    {
        $returnVal = false;
        $iDotPos = strrpos($sFileName, '.');
        if (false === $iDotPos) {
            return false;
        }
        $sFileExtension = substr($sFileName, $iDotPos + 1);

        $oFileType = new TCMSRecord('cms_filetype');
        $oFileType->LoadFromField('file_extension', $sFileExtension);

        if (!is_null($oFileType->id)) {
            $returnVal = $oFileType->sqlData['content_type'];
        }

        return $returnVal;
    }

    /**
     * Generates an export.
     *
     * @param string $sId
     *
     * @return string $sOutput
     */
    public function GetExport($sId = null)
    {
        $sOutput = '';
        $oTableConf = $this->GetFieldCmsTblConf();
        if ($oTableConf) {
            $sTableName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $oTableConf->fieldName);
            $oData = null;
            if (is_null($sId)) {
                $sRestriction = null;
                if (!empty($this->fieldRestriction)) {
                    $sRestriction = $this->fieldRestriction;
                }

                if ($sTableName && $oData = call_user_func(array($sTableName.'List', 'GetList'), $sRestriction)) {
                    /** @var $oData TCMSRecordList */
                    $sOutput = $this->RenderList($oData);
                }
            } else {
                if ($sTableName && $oData = call_user_func(array($sTableName, 'GetNewInstance'), $sId)) {
                    $sOutput = $this->Render($oData, $this->GetViewName());
                }
            }
        }

        return $sOutput;
    }

    /**
     * @return string
     *
     * @param string $sHeaderViewPath
     */
    protected function RenderHeader($sHeaderViewPath)
    {
        /** @var $oViewRenderer ViewRenderer */
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->setShowHTMLHints(false);
        $sOutput = $oViewRenderer->Render($sHeaderViewPath);

        return $sOutput;
    }

    /**
     * Renders an export view for given data and view path.
     *
     * @param TCMSRecord $oExportData
     * @param string     $sViewPath
     *
     * @return string $sOutput
     */
    protected function Render($oExportData, $sViewPath)
    {
        /** @var $oViewRenderer ViewRenderer */
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->setShowHTMLHints(false);
        $oViewRenderer->AddSourceObject('exportdata', $oExportData);
        $oViewRenderer->AddMapper(new GenericTableExportMapper());

        // Check if we have additional, profile-specific mappers
        if (!empty($this->fieldMapperConfig)) {
            $aMapperList = explode(';', $this->fieldMapperConfig);
            if (count($aMapperList) > 0) {
                foreach ($aMapperList as $sMapperConf) {
                    $aMapper = explode(',', $sMapperConf);
                    if (count($aMapper) > 0) {
                        $mapperIdentifier = $aMapper[0];
                        $oViewRenderer->addMapperFromIdentifier($mapperIdentifier);
                    }
                }
            }
        }

        return $oViewRenderer->Render($sViewPath);
    }

    /**
     * @return string
     */
    public function GetViewName()
    {
        if (empty($this->fieldViewPath)) {
            $sViewPath = 'pkgGenericTableExport/';
        } else {
            $sViewPath = $this->fieldViewPath.'/';
        }

        $sViewPath .= $this->fieldView.'.twig';

        return $sViewPath;
    }

    /**
     * @return string
     */
    public function GetHeaderViewName()
    {
        $sViewPath = '';

        if (!empty($this->fieldHeaderView)) {
            if (empty($this->fieldViewPath)) {
                $sViewPath = 'pkgGenericTableExport/';
            } else {
                $sViewPath = $this->fieldViewPath.'/';
            }

            $sViewPath .= $this->fieldHeaderView.'.twig';
        }

        return $sViewPath;
    }

    /**
     * Renders a list of records.
     *
     * @param TCMSRecordList $oExportData
     *
     * @return string $sOutput
     */
    protected function RenderList($oExportData)
    {
        $sOutput = '';
        $sHeaderViewPath = $this->GetHeaderViewName();
        if ('' !== $sHeaderViewPath) {
            $sOutput .= $this->RenderHeader($sHeaderViewPath)."\n";
        }

        $sViewPath = $this->GetViewName();
        while ($oRecord = $oExportData->Next()) {
            $sOutput .= $this->Render($oRecord, $sViewPath)."\n";
        }

        return $sOutput;
    }

    /**
     * @param string $sSystemName
     *
     * @return TdbPkgGenericTableExport|null
     */
    public static function GetInstanceFromSystemName($sSystemName)
    {
        $oGenericTableExport = TdbPkgGenericTableExport::GetNewInstance();
        if (!$oGenericTableExport->LoadFromField('system_name', $sSystemName)) {
            $oGenericTableExport = null;
        } else {
            $aData = $oGenericTableExport->sqlData;
            $sClassName = $aData['class'];
            $oGenericTableExport = new $sClassName();
            $oGenericTableExport->LoadFromRow($aData);
        }

        return $oGenericTableExport;
    }

    private function getExportDirectoryPath(): string
    {
        return PATH_CMS_CUSTOMER_DATA.'/'.$this->sExportPath.'/';
    }

    private function createExportDirectoryIfNeeded(): void
    {
        $dir = $this->getExportDirectoryPath();

        if (false === \is_dir($dir)) {
            \mkdir($dir, 0777, true);
        }
    }
}
