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
use Psr\Log\LoggerInterface;

/**
 * @psalm-suppress UndefinedDocblockClass
 * @psalm-suppress UndefinedClass
 *
 * @see https://github.com/chameleon-system/chameleon-system/issues/773
 */
class TPkgCsv2Sql extends TPkgCsv2SqlAutoParent
{
    /**
     * @var string
     */
    protected $sTimeStampFormat = 'Ymd_His';

    /**
     * @var bool
     */
    protected $bDebugMode = false;

    /**
     * @deprecated since 6.3.0 - not used anymore
     *
     * @var string
     */
    protected $sLogFileName = 'csv2sql_import.log';

    /**
     * @var array{
     *     start: int|null,
     *     end: int|null,
     *     bHasErrors: bool,
     * }
     */
    protected $aImportStats = ['start' => null, 'end' => null, 'bHasErrors' => false];

    /**
     * @psalm-suppress UndefinedDocblockClass - https://github.com/chameleon-system/chameleon-system/issues/773
     *
     * @var TPkgCmsBulkSql_LoadDataInfile
     */
    protected $oBulkInsert;

    /**
     * @return bool
     */
    public function hasImportErrors()
    {
        return isset($this->aImportStats['bHasErrors']) && true === $this->aImportStats['bHasErrors'];
    }

    /**
     * returns the name of the log file for active object.
     *
     * @return string
     *
     * @deprecated since 6.3.0 - not used anymore
     */
    public function GetLogFile()
    {
        return '';
    }

    /**
     * Log error message to logfile.
     *
     * @param string $sFileName
     * @param string $sFileLine
     * @param string $sMsg
     *
     * @return bool
     */
    protected function LogError($sMsg, $sFileName = null, $sFileLine = null)
    {
        $bRet = false;
        if (!empty($sMsg)) {
            if (!$this->aImportStats['bHasErrors']) {
                $this->aImportStats['bHasErrors'] = true;
            }

            $logger = $this->getLogger();
            $logger->error($sMsg);

            $bRet = true;
        }

        return $bRet;
    }

    /**
     * Create temp-table to import csv data into.
     *
     * @param array $aErrorList
     *
     * @return bool
     */
    protected function CreateTable(&$aErrorList)
    {
        $logger = $this->getLogger();

        $bRet = false;
        // DROP TABLE IF EXISTS  test1
        $databaseConnection = $this->getDatabaseConnection();
        $quotedTargetTableName = $databaseConnection->quoteIdentifier($this->GetTempTargetTableName());
        $sQry = "DROP TABLE IF EXISTS $quotedTargetTableName ";
        MySqlLegacySupport::getInstance()->query($sQry);
        $logger->info('create table drop '.MySqlLegacySupport::getInstance()->error());

        $sColumnSet = [];
        $aRowDefinition = $this->GetRowDef();
        foreach ($aRowDefinition as $iKey => $aRowDef) {
            if ('IGNORE' != strtoupper(trim($aRowDef['type']))) {
                $quotedName = $databaseConnection->quoteIdentifier($aRowDef['name']);
                $sColumnSet[] = "$quotedName ".$aRowDef['type'].' NOT NULL ';
            }
        }

        if (count($sColumnSet)) {
            $sColumns = implode(', ', $sColumnSet);
            $sQry = "CREATE TABLE $quotedTargetTableName ( ".$sColumns.' ) ENGINE = InnoDB ';

            MySqlLegacySupport::getInstance()->query($sQry);
            $sError = MySqlLegacySupport::getInstance()->error();
            $logger->info('create table  '.$sQry.' WITH ERROR: '.$sError);
            if ($sError) {
                $bRet = false;
                $this->LogError('SQL-ERROR in: '.$sQry."\n".$sError."\n---");
            } else {
                $bRet = true;
            }
        }
        if (false == $bRet) {
            $this->LogError('Cannot create table for '.$this->fieldDestinationTableName);
            $aErrorList[] = 'Cannot create table for '.$this->fieldDestinationTableName;
        }

        return $bRet;
    }

    /**
     * convert the target name passed into the tmp table name used during the import process.
     *
     * @return string
     */
    protected function GetTempTargetTableName()
    {
        return '_imptmp_'.$this->fieldDestinationTableName;
    }

    /**
     * convert the target name passed into the final table name used after the import process.
     *
     * @return string
     */
    protected function GetFinalTargetTableName()
    {
        return '_imp_'.$this->fieldDestinationTableName;
    }

    /**
     * Get valid log-file-name for this object.
     *
     * @return string
     *
     * @deprecated since 6.3.0 - not used anymore
     */
    public function CreateLogFileName()
    {
        return '';
    }

    /**
     * @return array
     */
    protected function GetRowDef()
    {
        $aRows = [];

        $aMapDef = explode("\n", trim($this->fieldColumnMapping));
        foreach ($aMapDef as $iK => $sRowDef) {
            $aRowTmp = explode('=', $sRowDef);
            $aRowinfo = explode(';', $aRowTmp[1]);

            if ('IGNORE' != strtoupper(trim($aRowinfo[0]))) {
                $aMappedRowInfo = ['name' => $aRowinfo[0], 'type' => $aRowinfo[1], 'convert' => '', 'index' => false];
                if (count($aRowinfo) > 2 && 'false' != $aRowinfo[2]) {
                    $aMappedRowInfo['convert'] = $aRowinfo[2];
                }
                if (count($aRowinfo) > 3 && 'false' != $aRowinfo[3]) {
                    $aMappedRowInfo['index'] = true;
                }
                foreach ($aMappedRowInfo as $sKey => $sVal) {
                    $aRowinfo[$sKey] = $sVal;
                }
                $aRows[] = $aRowinfo;
            } else {
                $aMappedRowInfo = ['name' => 'IGNORE', 'type' => 'IGNORE'];
                foreach ($aMappedRowInfo as $sKey => $sVal) {
                    $aRowinfo[$sKey] = $sVal;
                }
                $aRows[] = $aRowinfo;
            }
        }

        return $aRows;
    }

    /**
     * Commit (rename table prefix from "_imptmp_" to "_imp_").
     *
     * @internal param string $sTableName Name of table to commit
     *
     * @return bool|array
     */
    protected function CommitTable()
    {
        $bRet = false;
        $bResult = $this->oBulkInsert->CommitData();
        if (true !== $bResult) {
            return ['error importing data: '.$bResult];
        }

        $logger = $this->getLogger();

        $databaseConnection = $this->getDatabaseConnection();
        $quotedTempTargetTableName = $databaseConnection->quoteIdentifier($this->GetTempTargetTableName());
        $sQry = "SELECT COUNT(*) AS rowcount FROM $quotedTempTargetTableName ";
        $tRes = MySqlLegacySupport::getInstance()->query($sQry);
        $sError = MySqlLegacySupport::getInstance()->error();
        if (strlen(trim($sError)) > 0) {
            $this->LogError('SQL-ERROR in: '.$sQry."\n".$sError."\n---");
        } else {
            $oRes = MySqlLegacySupport::getInstance()->fetch_object($tRes);
            if ($oRes->rowcount) {
                $bRet = false;
                $quotedFinalTargetTableName = $databaseConnection->quoteIdentifier($this->GetFinalTargetTableName());
                $sQry = "DROP TABLE IF EXISTS $quotedFinalTargetTableName ";
                MySqlLegacySupport::getInstance()->query($sQry);
                $logger->info('drop table in commit table '.$sQry);

                $this->AddTableIndex();
                $sQry = "ALTER TABLE $quotedTempTargetTableName RENAME $quotedFinalTargetTableName ";
                MySqlLegacySupport::getInstance()->query($sQry);
                $sError = MySqlLegacySupport::getInstance()->error();
                if (strlen(trim($sError)) > 0) {
                    $this->LogError('SQL-ERROR in: '.$sQry."\n".$sError."\n---");
                    $bRet = false;
                } else {
                    $bRet = true;
                }
            } else {
                // no data found! remove table?
                $sQry = "DROP TABLE IF EXISTS $quotedTempTargetTableName ";
                MySqlLegacySupport::getInstance()->query($sQry);
                $logger->info('drop table because no data was found in CommitTable '.$sQry);
            }
        }

        return $bRet;
    }

    /**
     * Add index to table fields.
     *
     * @return bool
     */
    protected function AddTableIndex()
    {
        $bRet = true;
        $aRowDefinition = $this->GetRowDef();
        $aIndexDef = [];

        $databaseConnection = $this->getDatabaseConnection();
        foreach ($aRowDefinition as $iKey => $aRowDef) {
            if (isset($aRowDef['index']) && true == $aRowDef['index']) {
                $aIndexDef[] = $databaseConnection->quoteIdentifier($aRowDef['name']);
            }
        }
        if (count($aIndexDef) > 0) {
            $quotedTempTargetTableName = $databaseConnection->quoteIdentifier($this->GetTempTargetTableName());
            $sQry = "ALTER TABLE $quotedTempTargetTableName ADD INDEX (".implode('), ADD INDEX (', $aIndexDef).')';

            MySqlLegacySupport::getInstance()->query($sQry);
            $sError = MySqlLegacySupport::getInstance()->error();
            if ($sError) {
                $this->LogError('SQL-ERROR in: '.$sQry."\n".$sError."\n---");
                $bRet = false;
            } else {
                $bRet = true;
            }
        }

        return $bRet;
    }

    /**
     * TPkgCsv2Sql::Import
     * - move files to working directory
     * - create tmp table
     * - import data
     * - CommitTable:
     * - delete existing import table @todo: doesn't work!
     * - rename tmp table
     * - move files to archive directory.
     *
     * @param null $sLogName
     * @param IPkgCmsBulkSql|null $oBulkInsertManager alternative bulk insert manager if not passed TPkgCmsBulkSql_LoadDataInfile will be used
     *
     * @return array|bool - array of error strings or false on error
     *
     * @psalm-suppress UndefinedClass - https://github.com/chameleon-system/chameleon-system/issues/773
     */
    public function Import($sLogName = null, ?IPkgCmsBulkSql $oBulkInsertManager = null)
    {
        $logger = $this->getLogger();

        $this->aImportStats['start'] = time();

        $aRowDefinition = $this->GetRowDef();
        if (null === $oBulkInsertManager) {
            $oBulkInsertManager = new TPkgCmsBulkSql_LoadDataInfile();
        }
        $this->oBulkInsert = $oBulkInsertManager;
        $aFieldNames = [];
        foreach ($aRowDefinition as $aMapping) {
            if ('IGNORE' != strtoupper(trim($aMapping['type']))) {
                $aFieldNames[] = $aMapping['name'];
            }
        }
        if (false === $this->oBulkInsert->Initialize($this->GetTempTargetTableName(), $aFieldNames)) {
            // error initializing - exit
            $logger->info('CSV not processed because bulk import could not be initialized: '.$this->GetTempTargetTableName());

            return false;
        }

        $aResultArray = [];
        $aRes = [];

        $bDone = false;
        $sSource = trim($this->fieldSource);
        if ('/' != substr($sSource, 0, 1)) {
            $sSource = '/'.$sSource;
        }
        $sSource = PATH_CMS_CUSTOMER_DATA.$sSource;
        $sSource = str_replace('//', '/', $sSource);

        $sImportDir = $sSource;
        $bContainsWildcards = (false !== strpos($sSource, '*'));
        if ($bContainsWildcards) {
            $sImportDir = substr($sSource, 0, strrpos($sSource, '*'));
        }
        // directory
        if (!$bDone && is_dir($sImportDir) && is_readable($sImportDir)) {
            $logger->info('importing dir '.$sImportDir);
            $aRes = $this->_ImportDir($sSource);
            $bDone = true;
        }
        // file
        if (!$bDone && is_file($sSource) && file_exists($sSource) && is_readable($sSource)) {
            $logger->info('importing FILE '.$sImportDir);
            $bTableOK = true;
            if (!$this->CreateTable($aResultArray)) {
                $bTableOK = false;
            }
            if ($bTableOK) {
                $aRes = $this->_ImportFile($sSource);
                $this->CommitTable();
            }

            $bDone = true;
        }

        if (!$bDone) {
            $logger->notice('No files found!');
        }

        $aResultArray = self::ArrayConcat($aResultArray, $aRes);

        $this->aImportStats['end'] = time();

        return $aResultArray;
    }

    /**
     * Merge two error arrays.
     *
     * @static
     *
     * @param array $aFirst
     * @param array $aSecond
     *
     * @return array
     */
    public static function ArrayConcat($aFirst, $aSecond)
    {
        $aResult = [];
        if (is_array($aFirst) && count($aFirst)) {
            $aResult = $aFirst;
            if (is_array($aSecond) && count($aSecond)) {
                // array_push($aResult, $aSecond);
                $aResult = array_merge($aResult, $aSecond);
            }
        } else {
            if (is_array($aSecond) && count($aSecond)) {
                $aResult = $aSecond;
            }
        }

        return $aResult;
    }

    /**
     * @param string $sSource
     *
     * @return array|int|mixed
     */
    protected function _ImportDir($sSource)
    {
        $aResultArray = [];

        $sImportDir = $sSource;
        $sPattern = '*.csv';
        $bContainsWildcards = (false !== strpos($sSource, '*'));
        if ($bContainsWildcards) {
            $sImportDir = substr($sSource, 0, strrpos($sSource, '/'));
            $sPattern = substr($sSource, strrpos($sSource, '/') + 1);
        }

        $logger = $this->getLogger();

        if (is_dir($sImportDir) && is_readable($sImportDir)) {
            $logger->info('importing DIR FOUND '.$sImportDir);
            $oFileList = TCMSFileList::GetInstance($sImportDir, $sPattern, false);
            $oFileList->GoToStart();
            if ($oFileList->Length()) {
                $logger->info('importing DIR has length '.$sImportDir);
                $bTableOK = true;
                if (!$this->CreateTable($aResultArray)) {
                    $bTableOK = false;
                }
                if ($bTableOK) {
                    $logger->info('importing DIR import file '.$sImportDir);
                    while ($oFile = $oFileList->Next()) {
                        $aRes = $this->_ImportFile($oFile->sPath);
                        $aResultArray = self::ArrayConcat($aResultArray, $aRes);
                    }
                    $this->CommitTable();
                }
            }
        } else {
            $logger->info('importing DIR NOT FOUND '.$sImportDir);
        }

        return $aResultArray;
    }

    /**
     * @param string $sFileName
     *
     * @return string[]
     */
    protected function _ImportFile($sFileName)
    {
        $aResultArray = [];
        if (file_exists($sFileName)) {
            // start import ...
            $sWorkFilename = $this->MoveFileToWorkingDirectory($sFileName);

            $bTableOK = true;
            // do job ...
            if ($sWorkFilename) {
                // remove BOM if exists
                $this->removeUTF8BOM($sWorkFilename);
                // check rows
                $aRowDefinition = $this->GetRowDef();

                // Columns
                $aRowDefinition = $this->GetRowDef();
                $iCountColumnsToImport = count($aRowDefinition);
                foreach ($aRowDefinition as $iKey => $aRowDef) {
                    if ('IGNORE' == strtoupper(trim($aRowDefinition[$iKey]['type']))) {
                        $aRowDefinition[$iKey]['type'] = 'IGNORE';
                    }
                }
                reset($aRowDefinition);

                $iRowCount = 0;
                $handle = fopen($sWorkFilename, 'r');
                while (!feof($handle)) {
                    ++$iRowCount;
                    $buffer = fgets($handle);
                    $buffer = trim($buffer);
                    if ('utf-8' != strtolower($this->fieldSourceCharset) && !empty($this->fieldSourceCharset)) {
                        $buffer = iconv($this->fieldSourceCharset, 'UTF-8', $buffer);
                    }
                    $aRows = explode(';', $buffer);

                    // $old_locale = getlocale(LC_ALL);
                    // $old_locale = setlocale(LC_ALL, "de_DE");
                    // $aRows = fgetcsv($handle, 4096, ';', '"');
                    // setlocale(LC_ALL, $old_locale);

                    $iFileColumnCount = count($aRows);
                    if (1 === $iFileColumnCount && '' === trim($aRows[0])) {
                        $iFileColumnCount = 0;
                        $aRows = [];
                    }

                    if ($aRows && $iFileColumnCount) {
                        if ($iFileColumnCount != (int) $iCountColumnsToImport) {
                            if ($iFileColumnCount > 0) {
                                $sLastError = 'Column mismatch in ('.$this->fieldName.') line: '.$iRowCount.' file: '.$sWorkFilename;
                                $this->LogError($sLastError);
                                $aResultArray[] = $sLastError;
                            }
                        } else {
                            $aDataCol = [];
                            reset($aRowDefinition);
                            foreach ($aRowDefinition as $iKey => $aRowDef) {
                                if ('IGNORE' != $aRowDef['type']) {
                                    $sValue = $aRows[$iKey];

                                    // check if value starts and ends with double quotes
                                    if ('"' === substr($sValue, 0, 1) && '"' === substr($sValue, -1, 1)) {
                                        // strip of double quotes from start and end
                                        $sValue = substr($sValue, 1, -1);

                                        // replace each double double quotes in the value with single double quotes
                                        $sValue = str_replace('""', '"', $sValue);
                                    }

                                    if (!empty($aRowDef['convert']) && method_exists($this, $aRowDef['convert'])) {
                                        $sValue = call_user_func_array([$this, $aRowDef['convert']], [$sValue]);
                                    }

                                    $aDataCol[] = $sValue;
                                }
                            }
                            $this->oBulkInsert->AddRow($aDataCol);
                        }
                    }
                }
                fclose($handle);
            }

            // move file to archive
            $this->MoveFileToArchiveDirectory($sWorkFilename);
        }

        return $aResultArray;
    }

    /**
     * TPkgCsv2Sql:: Validate
     * - move files to working directory
     * - check if the file exists
     * - check if the defined columns exists
     * - move files back to source directory
     * - return an array of error messages (or an empty array if there are no messages).
     *
     * @return array
     */
    public function Validate()
    {
        $aResultArray = [];

        $bDone = false;
        $sSource = trim($this->fieldSource);
        if ('/' != substr($sSource, 0, 1)) {
            $sSource = '/'.$sSource;
        }
        $sSource = PATH_CMS_CUSTOMER_DATA.$sSource;
        $sSource = str_replace('//', '/', $sSource);

        // directory
        if (!$bDone && is_dir($sSource) && is_readable($sSource)) {
            $aRes = $this->_ValidateDir($sSource);
            $aResultArray = self::ArrayConcat($aResultArray, $aRes);
            $bDone = true;
        }
        // file
        if (!$bDone && is_file($sSource) && file_exists($sSource) && is_readable($sSource)) {
            $aRes = $this->_ValidateFile($sSource);
            $aResultArray = self::ArrayConcat($aResultArray, $aRes);
            $bDone = true;
        }

        if (false === $bDone) {
            $logger = $this->getLogger();
            $logger->notice('Invalid file or directory: '.$sSource);
        }

        return $aResultArray;
    }

    /**
     * @param string $sSource
     *
     * @return array
     */
    protected function _ValidateDir($sSource)
    {
        $aResultArray = [];
        if (is_dir($sSource) && is_readable($sSource)) {
            $oFileList = TCMSFileList::GetInstance($sSource, '*.csv', false);
            $oFileList->GoToStart();
            if ($oFileList->Length()) {
                while ($oFile = $oFileList->Next()) {
                    $aRes = $this->_ValidateFile($oFile->sPath);
                    $aResultArray = self::ArrayConcat($aResultArray, $aRes);
                }
            }
        } else {
            $this->LogError('Directory not readable or not found: '.$sSource);
        }

        return $aResultArray;
    }

    /**
     * @param string $SourceFile
     *
     * @return string[]
     */
    protected function _ValidateFile($SourceFile)
    {
        $aResultArray = [];
        if (is_file($SourceFile) && file_exists($SourceFile) && is_readable($SourceFile)) {
            // move file to work dir
            $sWorkingOnFilename = $this->MoveFileToWorkingDirectory($SourceFile);
            if ($sWorkingOnFilename) {
                // remove BOM if exists
                $this->removeUTF8BOM($sWorkingOnFilename);
                // check rows
                $handle = fopen($sWorkingOnFilename, 'r');
                while (!feof($handle)) {
                    $aRows = fgetcsv($handle, 0, ';', '"');
                    $iFileRowCount = count($aRows);
                    if ($aRows && $iFileRowCount) {
                        $iMapDefRowCount = count(explode("\n", trim($this->fieldColumnMapping)));
                        if ((int) $iFileRowCount != (int) $iMapDefRowCount) {
                            $sLastError = 'Column mismatch in ('.$this->fieldName.') file: '.$sWorkingOnFilename;
                            $this->LogError($sLastError);
                            $aResultArray[] = $sLastError;
                        }
                        if (file_exists($sWorkingOnFilename)) {
                            if (!$this->MoveFileToSourceDirectory($sWorkingOnFilename)) {
                                $sLastError = "Can't move file: ".$sWorkingOnFilename;
                                $this->LogError($sLastError);
                                $aResultArray[] = $sLastError;
                            }
                        }
                    }
                    break;
                }
                fclose($handle);
            } else {
                $sLastError = "Can't move file: ".$SourceFile;
                $this->LogError($sLastError);
                $aResultArray[] = $sLastError;
            }
        } else {
            $this->LogError('File not readable or not found: '.$SourceFile);
        }

        return $aResultArray;
    }

    /**
     * Check if UTF8 BOM exists.
     *
     * @param string $sFilename
     *
     * @return bool|null
     */
    protected function _CheckUTF8BOMExists($sFilename = '')
    {
        if (file_exists($sFilename)) {
            if (is_file($sFilename) && is_readable($sFilename)) {
                $handle = fopen($sFilename, 'r');
                if (filesize($sFilename) < 1024) {
                    $contents = fread($handle, filesize($sFilename));
                } else {
                    $contents = fread($handle, 1024);
                }
                fclose($handle);

                return substr($contents, 0, 3) == pack('CCC', 0xEF, 0xBB, 0xBF);
            } else {
                $this->LogError($sFilename.' is not a file or not readable');
            }
        } else {
            $this->LogError($sFilename.' does not exist!');
        }
    }

    /**
     * Remove BOM from string.
     *
     * @param string $str
     *
     * @return string
     */
    protected function _removeBOM($str = '')
    {
        if (substr($str, 0, 3) == pack('CCC', 0xEF, 0xBB, 0xBF)) {
            $str = substr($str, 3);
        }

        return $str;
    }

    /**
     * Check if there is an BOM and remove it.
     *
     * @param string $csvfile
     *
     * @return void
     */
    public function removeUTF8BOM($csvfile = '')
    {
        if ($this->_CheckUTF8BOMExists($csvfile)) {
            $utfcheck = file_get_contents($csvfile);
            $utfcheck = $this->_removeBOM($utfcheck);
            $fp = fopen($csvfile.'.NOBOM', 'wb');
            fwrite($fp, $utfcheck);
            fclose($fp);
            rename($csvfile.'.NOBOM', $csvfile);
        }
    }

    /**
     * @param string $SourceFile
     *
     * @return false|string
     */
    protected function MoveFileToWorkingDirectory($SourceFile)
    {
        return $this->MoveFile($SourceFile, 'working');
    }

    /**
     * @param string $SourceFile
     *
     * @return false|string
     */
    protected function MoveFileToArchiveDirectory($SourceFile)
    {
        return $this->MoveFile($SourceFile, 'archive');
    }

    /**
     * @param string $SourceFile
     *
     * @return false|string
     */
    protected function MoveFileToSourceDirectory($SourceFile)
    {
        return $this->MoveFile($SourceFile, 'incoming');
    }

    /**
     * Move file to new directory.
     *
     * @param string $sDestinationType
     * @param string $SourceFile
     *
     * @return false|string
     */
    protected function MoveFile($SourceFile, $sDestinationType = 'working')
    {
        $sRet = true;
        $sArchiveDir = '';
        $sTargetFileName = '';

        $SourceFile = trim($SourceFile);
        if (!empty($SourceFile) && file_exists($SourceFile)) {
            $aPathParts = pathinfo($SourceFile);
            $sTargetFileName = $aPathParts['basename'];
            $sDestinationType = strtoupper(trim($sDestinationType));

            // if we are in debugging-mode - don't move files to "archive"
            if ($this->bDebugMode && ('ARCHIVE' === $sDestinationType)) {
                $sDestinationType = 'INCOMING';
            }

            switch ($sDestinationType) {
                case 'WORKING':
                    $sArchiveDir = $aPathParts['dirname'].'/../working/';
                    $sTargetFileName = date($this->sTimeStampFormat).'-'.$sTargetFileName;
                    break;
                case 'ARCHIVE':
                    $sArchiveDir = $aPathParts['dirname'].'/../archive/';
                    // add new timestamp
                    $sTargetFileName = substr($sTargetFileName, strpos($sTargetFileName, '-') + 1);
                    $sTargetFileName = date($this->sTimeStampFormat).'-'.$sTargetFileName;
                    break;
                case 'INCOMING':
                case 'SOURCE':
                    $sArchiveDir = $aPathParts['dirname'].'/../incoming/';
                    $sTargetFileName = substr($sTargetFileName, strpos($sTargetFileName, '-') + 1);
                    break;
                default:
                    $sArchiveDir = '';
            }
            if (!empty($sArchiveDir) && !is_dir($sArchiveDir) && !file_exists($sArchiveDir)) {
                if (!mkdir($sArchiveDir, 0777, true)) {
                    $sRet = false;
                    $this->LogError("Can't create: ".$sArchiveDir);
                }
            }
        }

        if ($sRet) {
            $sArchiveFilename = $sArchiveDir.$sTargetFileName;
            if (!empty($sArchiveFilename)) {
                // if(copy($SourceFile, $sArchiveFilename)) {
                if (@rename($SourceFile, $sArchiveFilename)) {
                    $sRet = $sArchiveFilename;
                } else {
                    $this->LogError("Can't rename: ".$SourceFile.' to '.$sArchiveFilename);
                    $sRet = false;
                }
            } else {
                $sRet = false;
            }
        }

        return $sRet;
    }

    /**
     * @param string $sSource
     *
     * @return string
     */
    protected function ConvertFromGermanDecimal($sSource)
    {
        $sSource = str_replace('.', '', $sSource);
        $sSource = str_replace(',', '.', $sSource);

        return $sSource;
    }

    /**
     * @param string $sSource
     *
     * @return string
     */
    protected function ConvertFromGermanDate($sSource)
    {
        static $oLocal = null;
        if (is_null($oLocal)) {
            $oLocal = TdbCmsLocals::GetNewInstance();
            $oLocal->LoadFromRow(['date_format' => 'd.m.y', 'time_format' => 'h:m:s', 'numbers' => '2|,|.']);
        }
        if (!empty($sSource)) {
            $aParts = explode(' ', $sSource);
            $aDatePart = explode('.', $aParts[0]);
            if (2 == strlen($aDatePart[2])) {
                $sPrefix = '20';
                $sDate = $sPrefix.$aDatePart[2];
                if (intval($sDate) > date('Y')) {
                    $sPrefix = '19';
                }
                $aDatePart[2] = $sPrefix.$aDatePart[2];
                $aParts[0] = implode('.', $aDatePart);
                $sSource = implode(' ', $aParts);
            }

            $sSource = $oLocal->StringToDate($sSource);
        }

        return $sSource;
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('monolog.logger.csv2sql');
    }
}
