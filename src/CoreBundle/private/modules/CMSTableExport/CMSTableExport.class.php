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

/**
 * exports table data as TAB, CSV or RTF.
 * /**/
class CMSTableExport extends TCMSModelBase
{
    protected $listParams;
    protected $oTableManager;
    protected $tableID;

    /**
     * TCMSTableConf object of table based on "tableID".
     *
     * @var TCMSTableConf
     */
    protected $oTableConf;

    /**
     * holds the full group table object.
     *
     * @var TFullGroupTable
     */
    protected $oTableList;

    /*
     * path to temp file
     *
     * @string
     */
    protected $sTempFilePath = '';

    /**
     * @var resource|null
     */
    protected $pTempFilePointer;

    public function Init()
    {
        $this->tableID = $this->global->GetUserData('tableID');
        if (!empty($this->tableID)) {
            /** @var $oCmsTblConf TdbCmsTblConf */
            $oTableConf = TdbCmsTblConf::GetNewInstance();
            $oTableConf->Load($this->tableID);
            $this->oTableConf = $oTableConf;
        }
    }

    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['tableID'] = $this->global->GetUserData('tableID');

        $this->data['listClass'] = $this->global->GetUserData('listClass');
        $this->data['listCacheKey'] = $this->global->GetUserData('listCacheKey');

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $aPortals = $securityHelper->getUser()?->getPortals();
        if (null === $aPortals) {
            $aPortals = [];
        }

        $aPortals = TTools::MysqlRealEscapeArray(array_keys($aPortals));
        $portals = "'".implode("','", $aPortals)."'";

        $profileOptions = '';
        if (!empty($portals)) {
            $query = 'SELECT * FROM `cms_export_profiles` WHERE `cms_portal_id` IN ('.$portals.") AND `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->tableID)."'";
            $oRecordList = new TCMSRecordList(); /* @var $oRecordList TCMSRecordList */
            $oRecordList->sTableName = 'cms_export_profiles';
            $oRecordList->Load($query);

            while ($oRecord = $oRecordList->Next()) { /* @var $oRecord TCMSRecord */
                $portalQuery = "SELECT `name` FROM `cms_portal` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oRecord->sqlData['cms_portal_id'])."'";
                $portalResult = MySqlLegacySupport::getInstance()->query($portalQuery);
                $portalRow = MySqlLegacySupport::getInstance()->fetch_assoc($portalResult);

                $name = $oRecord->GetName();
                if (!empty($name)) {
                    $profileOptions .= '<option value="'.TGlobal::OutHTML($oRecord->id).'">'.TGlobal::OutHTML($name).' ['.$portalRow['name']."]</option>\n";
                }
            }
        }

        $this->data['profileOptions'] = $profileOptions;

        $this->data['listName'] = 'cmstablelistObj'.$this->global->GetUserData('tableCmsIdentID');

        return $this->data;
    }

    /**
     * generates the export file using the list object from session.
     */
    public function GenerateExport()
    {
        $this->provideExportMemory();

        $listClass = $this->global->GetUserData('listClass');

        if (empty($listClass)) {
            $listClass = null;
        }

        $this->oTableList = $this->oTableConf->GetListObject($listClass)->tableObj;

        $sListCacheKey = $this->global->GetUserData('listCacheKey');

        if (!array_key_exists('_listObjCache', $_SESSION)) {
            $_SESSION['_listObjCache'] = [];
        }
        $objectInSession = array_key_exists($sListCacheKey, $_SESSION['_listObjCache']);

        if ($objectInSession) {
            $tmp = base64_decode($_SESSION['_listObjCache'][$sListCacheKey]);
            if (function_exists('gzcompress')) {
                $tmp = gzuncompress($tmp);
            }
            $this->oTableList = unserialize($tmp);
        }

        $this->oTableList->Display();

        $export_profile_id = $this->global->GetUserData('cms_export_profile_id');

        $oProfileRecord = TdbCmsExportProfiles::GetNewInstance();
        $oProfileRecord->Load($export_profile_id);

        $aFieldConfig = $this->FetchFieldConf($oProfileRecord);

        $sFileType = 'txt';
        if ('CSV' == $oProfileRecord->sqlData['export_type']) {
            $this->GenerateCSVExport($aFieldConfig, ';');
        } elseif ('TABs' == $oProfileRecord->sqlData['export_type']) {
            $this->GenerateCSVExport($aFieldConfig, "\t");
        }

        // close temp file handler
        if (!is_null($this->pTempFilePointer)) {
            fclose($this->pTempFilePointer);
        }
        $this->getDownload($sFileType);
    }

    private function provideExportMemory(): void
    {
        $exportMemoryUnits = ServiceLocator::getParameter('chameleon_system.core.export_memory');

        $configuredBytes = $this->unitToInt($exportMemoryUnits);
        $currentBytes = $this->unitToInt(ini_get('memory_limit'));

        if ($configuredBytes > $currentBytes) {
            ini_set('memory_limit', $exportMemoryUnits);
        }
    }

    /**
     * Converts a number with byte unit (B / K / M / G) into an integer of bytes.
     */
    private function unitToInt(string $units): int
    {
        if ('' === $units || false === strpos('BKMG', substr($units, -1))) {
            return 0;
        }

        return (int) preg_replace_callback('/(\-?\d+)(.?)/', function ($matches) {
            return $matches[1] * (1024 ** strpos('BKMG', $matches[2]));
        }, strtoupper($units));
    }

    /**
     * forces a download of the export file.
     *
     * @param string $sFileType
     */
    public function getDownload($sFileType)
    {
        $sTmpName = $this->getTempFilePath();
        if (file_exists($sTmpName)) {
            header('Set-Cookie: fileDownload=true; path=/');
            header('Content-Description: File Transfer');
            header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            // force download dialog
            if (false === strpos(php_sapi_name(), 'cgi')) {
                header('Content-Type: application/force-download');
                header('Content-Type: application/octet-stream', false);
                header('Content-Type: application/download', false);
            } else {
                header('Content-Type: application/octet-stream');
            }
            // use the Content-Disposition header to supply a recommended filename
            $sNiceFileName = TTools::sanitizeFilename($this->getExportFileName($sFileType));

            header('Content-Disposition: attachment; filename="'.$sNiceFileName.'";');
            header('Content-Transfer-Encoding: binary');

            header('Content-Length: '.(string) filesize($sTmpName));
            $file = fopen($sTmpName, 'r');
            fpassthru($file);
            fclose($file);
            unlink($sTmpName);
            exit;
        }
    }

    /**
     * loads configured field list for the export.
     *
     * @param TdbCmsExportProfiles $oProfileRecord
     *
     * @return array
     */
    protected function FetchFieldConf($oProfileRecord)
    {
        $aFieldConfig = [];
        $oFieldRecordList = $oProfileRecord->GetFieldCmsExportProfilesFieldsList();
        $oFieldRecordList->ChangeOrderBy(['sort_order' => 'ASC']);
        /** @var $oField TCMSRecord */
        while ($oField = $oFieldRecordList->Next()) {
            $aFieldConfig[$oField->sqlData['fieldname']] = $oField->sqlData['html_template'];
        }

        return $aFieldConfig;
    }

    /**
     * loads the recordlist (autoObject).
     *
     * @return TCMSRecordList
     */
    protected function GetRecordListObject()
    {
        $query = $this->oTableList->fullQuery;
        $this->oTableConf->sqlData['name'];
        $sClassName = TCMSTableToClass::GetClassName('Tdb', $this->oTableConf->sqlData['name']).'List';
        $oRecordsList = call_user_func_array([$sClassName, 'GetList'], [$query, null, false, true, true]);

        return $oRecordsList;
    }

    /**
     * generates a CSV file using delimiter ";" or "\t".
     *
     * @param array $aFieldConfig - contains TCMSRecord field objects
     * @param string $delimiter - default ";"
     */
    protected function GenerateCSVExport($aFieldConfig, $delimiter = ';')
    {
        $this->SetTemplate('CMSTableExport', 'csv');
        $oRecordList = $this->GetRecordListObject();

        $oTableConf = TdbCmsTblConf::GetNewInstance($this->tableID);

        $count = 0;
        /** @var $oRecord TCMSRecord */
        while ($oRecord = $oRecordList->Next()) {
            $csvData = '';
            reset($aFieldConfig);

            // export column names in first row
            if (0 == $count) {
                $csvData .= '"ID"'.$delimiter;
                foreach ($aFieldConfig as $sFieldName => $htmlTemplate) {
                    if (!empty($sFieldName)) {
                        $oField = $oTableConf->GetField($sFieldName, $oRecord);
                        $csvData .= '"'.$this->FilterCSVData($oField->oDefinition->fieldTranslation).'"'.$delimiter;
                        unset($oField);
                    }
                }

                $csvData .= "\n";
                reset($aFieldConfig);
            }

            /*
             * add cmsident.
             */
            $csvData .= '"'.$oRecord->sqlData['cmsident'].'"'.$delimiter;
            foreach ($aFieldConfig as $sFieldName => $htmlTemplate) {
                if (!empty($sFieldName)) {
                    $oField = $oTableConf->GetField($sFieldName, $oRecord);
                    if (is_object($oField)) {
                        $text = $oField->GetHTMLExport();
                        // $text = strip_tags($text);
                        if (stristr($htmlTemplate, '@text@')) {
                            $text = str_replace('@text@', $text, $htmlTemplate);
                            $text = $this->FilterCSVData($text);
                            $csvData .= '"'.$text.'"'.$delimiter;
                        } else {
                            $text = $this->FilterCSVData($text);
                            $csvData .= '"'.$text.'"'.$delimiter;
                        }
                    }
                    unset($oField);
                }
            }

            // kill last delimiter
            if (';' == substr($csvData, -1, 1)) {
                $csvData = substr($csvData, 0, -1);
            }

            $csvData .= "\n";
            ++$count;

            $this->writeTempFile($csvData);
            unset($csvData);
            unset($oRecord);
            // gc_collect_cycles();
        }
    }

    /**
     * filters CSV field values by different quote types newlines etc.
     *
     * @param string $csvData
     *
     * @return string
     */
    protected function FilterCSVData($csvData = '')
    {
        $csvData = nl2br($csvData);

        $csvData = iconv('UTF-8', 'CP1252', $csvData);

        $csvData = str_replace('„', '"', $csvData);
        $csvData = str_replace('“', '"', $csvData);
        $csvData = str_replace('"', '""', $csvData);

        $csvData = html_entity_decode($csvData, ENT_QUOTES, 'ISO8859-15');

        // kill all newlines
        // $csvData = nl2br($csvData);

        $csvData = strip_tags($csvData);
        $csvData = str_replace("\r\n", ' ', $csvData);
        $csvData = str_replace("\n", ' ', $csvData);
        $csvData = str_replace("\t", ' ', $csvData);

        $csvData = preg_replace('/\s\s+/', ' ', $csvData);

        return trim($csvData);
    }

    /**
     * note: RTF export is disabled at the moment, needs rework with
     * new PHP5 RTF Generator class.
     *
     * generates export file in RTF format
     * uses GetRTFExport() method of field objects
     *
     * @param array $aFieldConfig
     */
    protected function GenerateRTFExport($aFieldConfig)
    {
        $this->SetTemplate('CMSTableExport', 'rtf');
        $oTableConf = TdbCmsTblConf::GetNewInstance($this->tableID);

        $oRecordList = $this->GetRecordListObject();
        /** @var $oRecord TCMSRecord */
        while ($oRecord = $oRecordList->Next()) {
            $rtfData = '';
            reset($aFieldConfig);
            foreach ($aFieldConfig as $sFieldName => $htmlTemplate) {
                if (!empty($sFieldName)) {
                    $oField = $oTableConf->GetField($sFieldName, $oRecord);
                    $text = $oField->GetRTFExport();

                    if (stristr($htmlTemplate, '@text@')) {
                        $rtfData .= str_replace('@text@', $text, $htmlTemplate).'<br>';
                    } else {
                        $rtfData .= $text.'<br>';
                    }
                    unset($oField);
                }
            }

            // page breaks should be configurable via export profile
            // $rtfData .= "<new page>";
            $rtfData .= '<br><br><br>';
            $this->writeTempFile($rtfData);
        }
    }

    /**
     * writes row to temp file.
     *
     * @param string $sRow
     */
    protected function writeTempFile($sRow)
    {
        $sFilePath = $this->getTempFilePath();

        // init file pointer
        if (is_null($this->pTempFilePointer)) {
            if (!$this->pTempFilePointer = fopen($sFilePath, 'a+b')) {
                TTools::WriteLogEntry('Could not write export temp file: '.$sFilePath, 1, __FILE__, __LINE__);
            }
        }

        // write into temp file
        if (!fwrite($this->pTempFilePointer, $sRow)) {
            TTools::WriteLogEntry('Could not write into export temp file: '.$sFilePath, 1, __FILE__, __LINE__);
        }
    }

    /**
     * returns the path to the temporary file.
     *
     * @return string
     */
    protected function getTempFilePath()
    {
        if (empty($this->sTempFilePath)) {
            $filePath = tempnam(CMS_TMP_DIR, 'table_export_');
            $this->sTempFilePath = $filePath;
        }

        return $this->sTempFilePath;
    }

    /**
     * @param string $sFileExtension
     *
     * @return string
     */
    protected function getExportFileName($sFileExtension = '')
    {
        if (empty($sFileExtension)) {
            $sFileExtension = 'tmp';
        }
        $sFileName = $this->oTableConf->sqlData['name'].'_export_'.date('Y_m_d_H_i_s').'.'.$sFileExtension;

        return $sFileName;
    }

    /**
     * filters field values for RTF use.
     *
     * @param string $rtfData
     *
     * @return string
     */
    protected function FilterHTML4RTF($rtfData)
    {
        // replace &nbsp;
        $rtfData = str_replace('&nbsp;', ' ', $rtfData);

        $rtfData = str_replace('„', '"', $rtfData);
        $rtfData = str_replace('“', '"', $rtfData);

        $rtfData = iconv('UTF-8', 'CP1252', $rtfData);
        $rtfData = html_entity_decode($rtfData, ENT_QUOTES, 'ISO8859-15');

        // convert tags to lowercase
        $rtfData = preg_replace(',<(/?)([a-zA-Z]+)([^>]+)?(/?)>,ie', '"<\1".strtolower("\2")."\3\4>"', $rtfData);

        $rtfData = str_replace('<br />', '<br>', $rtfData);

        // strip styles
        $rtfData = preg_replace('/ class="(.*?)"/i', '', $rtfData);
        $rtfData = preg_replace('/ style="(.*?)"/i', '', $rtfData);
        $rtfData = preg_replace('/ align="(.*?)"/i', '', $rtfData);

        // strip ampercants from tag attributes
        $matchString = '|([^=]*?)=["]([^"]*?)["](.*?)|si';
        $rtfData = preg_replace($matchString, '\\1=\\2\\3', $rtfData);

        $rtfData = str_replace('<strong>', '<b>', $rtfData);
        $rtfData = str_replace('</strong>', '</b>', $rtfData);
        // $rtfData = str_replace('</p>', '', $rtfData);
        // $rtfData = str_replace('<p>', '<p after=10>', $rtfData);
        $rtfData = str_replace(' <br> ', '<br>', $rtfData);
        $rtfData = str_replace(' <br>', '<br>', $rtfData);
        $rtfData = str_replace('<br> ', '<br>', $rtfData);
        $rtfData = str_replace('<em>', '<i>', $rtfData);
        $rtfData = str_replace('</em>', '</i>', $rtfData);
        $rtfData = str_replace('</div>', '<br>', $rtfData);

        // hmmm don`t know if this is needed anymore
        $rtfData = str_replace('\\\\\\', '', $rtfData);

        // remove empty tags
        $pattern = "/<(?!br¦img¦hr¦\/)[^>]*>\s*<\/[^>]*>/";
        $rtfData = preg_replace($pattern, '', $rtfData);

        $allowedTags = '<b><i><u><sup><sub><br><hr><font><p><table><tr><td><new><header><footer>';
        $rtfData = strip_tags($rtfData, $allowedTags);

        $rtfData = preg_replace('/\s\s+/', ' ', $rtfData);

        // make XHTML compliant code
        require_once PATH_LIBRARY.'/classes/components/HTMLPurifier/HTMLPurifier.standalone.php';
        $purifier = new HTMLPurifier();
        // $config = HTMLPurifier_Config::createDefault();
        // $config->set('Core', 'XHTML', false);
        $rtfData = $purifier->purify($rtfData);

        return trim($rtfData);
    }

    /**
     * filters xHTML tags (MS Office Word only reads standard 90s HTML).
     *
     * @param string $exportData
     *
     * @return string
     */
    protected function FilterHTML($exportData)
    {
        // replace &nbsp;
        $exportData = str_replace('&nbsp;', ' ', $exportData);

        // $exportData = utf8_decode($exportData);

        // convert tags to lowercase
        $exportData = preg_replace(',<(/?)([a-zA-Z]+)([^>]+)?(/?)>,ie', '"<\1".strtolower("\2")."\3\4>"', $exportData);
        $exportData = str_replace('<br />', '<br>', $exportData);

        $exportData = str_replace('<strong>', '<b>', $exportData);
        $exportData = str_replace('</strong>', '</b>', $exportData);
        $exportData = str_replace(' <br> ', '<br>', $exportData);
        $exportData = str_replace(' <br>', '<br>', $exportData);
        $exportData = str_replace('<br> ', '<br>', $exportData);
        $exportData = str_replace('<em>', '<i>', $exportData);
        $exportData = str_replace('</em>', '</i>', $exportData);
        // $exportData = str_replace('</div>', '<br>', $exportData);

        $allowedTags = '<b><i><u></u><sup><sub><br><br /><hr><hr /><p></p><table><tr><td><tbody><div>';
        $exportDataBak = $exportData;
        $exportData = strip_tags($exportData, $allowedTags);
        if (empty($exportData)) {
            $exportData = $exportDataBak;
        }

        $exportData = preg_replace('/\s\s+/', ' ', $exportData);

        return trim($exportData);
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();

        $externalFunctions = ['GenerateExport'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/fileDownload/jquery.fileDownload.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }
}

/*
* Callback function for RTF A4 dimensions conversion
*/
function calcWidth($matches)
{
    $pagewidth = 210; // DINA4 = 210mm
    $htmlwidth = 900;

    if (stristr($matches[1], '%')) {
        $newWidth = $matches[1];
    } else {
        // Dreisatz :-)
        $newWidth = round(($pagewidth * $matches[1]) / $htmlwidth);
    }

    $returnVal = ' width='.$newWidth;

    return $returnVal;
}

function stripAmpsFromTags($matches)
{
    $returnVal = ' ='.$matches[1];

    return $returnVal;
}
