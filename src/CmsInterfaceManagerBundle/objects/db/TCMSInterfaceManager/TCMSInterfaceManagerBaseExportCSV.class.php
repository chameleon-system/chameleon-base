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
 * you can use extensions of this class to implement csv exports
 * OVERWRITE THE METHODS GetDataList, GetFieldMapping and GetExportRowFromDataObject (last one only if you need to)
 * you should not need to overwrite anything else.
 *
 * @template TItem extends TCMSRecord
 */
class TCMSInterfaceManagerBaseExportCSV extends TCMSInterfaceManagerBase
{
    public const TMP_TBL_PREFIX = '_tmpexport';

    /**
     * write csv filepath to this variable so that the calling class can get the created csv file.
     *
     * @var string
     */
    public $sCSVFilePath = '';

    /**
     * OVERWRITE THIS TO FETCH THE DATA. MUST RETURN A TCMSRecordList.
     *
     * @return TCMSRecordList<TItem>
     *
     * @psalm-suppress InvalidReturnType, InvalidReturnStatement
     */
    protected function GetDataList()
    {
        return TdbCmsMediaList::GetList();
    }

    /**
     * list all your fields that you want to export here.
     * OVERWRITE THIS METHOD TO DEFINE THE FILEDS YOU WANT TO EXPORT.
     *
     * @return array
     */
    protected function GetFieldMapping()
    {
        $aFields = ['datecreated' => 'DATETIME NOT NULL', 'somefield' => 'VARCHAR( 255 ) NOT NULL'];

        return $aFields;
    }

    /**
     * OVERWRITE THIS IF YOU NEED TO ADD ANY OTHER DATA TO THE ROW OBJECT.
     *
     * @param TItem $oDataObjct
     *
     * @return array
     */
    protected function GetExportRowFromDataObject($oDataObjct)
    {
        return $oDataObjct->sqlData;
    }

    /**
     * @return string
     *
     * @throws TPkgCmsException_Log
     */
    protected function GetExportTableName()
    {
        static $sTable;
        if (!$sTable) {
            $sTable = MySqlLegacySupport::getInstance()->real_escape_string(self::TMP_TBL_PREFIX.md5(uniqid((string) rand(), true)));
        }

        return $sTable;
    }

    /**
     * prepare the import - setup temp tables, download product feeds, etc
     * return false if the preparation failed.
     *
     * @return bool
     */
    protected function PrepareImport()
    {
        parent::PrepareImport();
        $bPreparationOk = true;

        $aFields = $this->GetFieldMapping();
        $sDDLFields = '';
        foreach ($aFields as $sName => $sType) {
            $sDDLFields .= "`{$sName}` {$sType},\n";
        }

        $query = 'CREATE TEMPORARY TABLE `'.$this->GetExportTableName()."` (
                {$sDDLFields}
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
                ) ";
        MySqlLegacySupport::getInstance()->query($query);

        return $bPreparationOk;
    }

    /**
     * this method is always called at the end of RunImport (even if the import failed) to do any cleanup work.
     *
     * @param bool $bImportSucceeded - set to true if the import succeeded
     *
     * @return bool
     */
    protected function Cleanup($bImportSucceeded)
    {
        parent::Cleanup($bImportSucceeded);
        $query = 'DROP TABLE `'.$this->GetExportTableName().'`';
        MySqlLegacySupport::getInstance()->query($query);

        return true;
    }

    /**
     * save order row to tmp table.
     *
     * @param array $aRow
     *
     * @return void
     */
    protected function SaveRow($aRow)
    {
        $aFields = $this->GetFieldMapping();
        $aInsert = [];
        foreach (array_keys($aFields) as $sKeyName) {
            if (array_key_exists($sKeyName, $aRow)) {
                $aFields[$sKeyName] = $aRow[$sKeyName];
            } else {
                $aFields[$sKeyName] = '';
            }
            $aInsert[] = '`'.MySqlLegacySupport::getInstance()->real_escape_string($sKeyName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($aFields[$sKeyName])."'";
        }

        $query = 'INSERT INTO `'.$this->GetExportTableName().'` SET '.implode(', ', $aInsert);
        MySqlLegacySupport::getInstance()->query($query);
        $sqlError = MySqlLegacySupport::getInstance()->error();
        if (!empty($sqlError)) {
            trigger_error('SQL Error: '.$sqlError, E_USER_WARNING);
        }
    }

    /**
     * perform the actual import work.
     *
     * @return bool true if the import succeeds, else false
     */
    protected function PerformImport()
    {
        parent::PerformImport();
        $bImportSucceeded = true;

        $oDataList = $this->GetDataList();
        while ($oData = $oDataList->Next()) {
            $aRow = $this->GetExportRowFromDataObject($oData);
            $this->SaveRow($aRow);
        }

        $this->CreateCSV();

        $sMsg = 'Es wurden '.$oDataList->Length().' Datensätze exportiert. Sie können die CSV hier herunterladen:<br />';
        $sMsg .= '<a href="'.URL_OUTBOX.'/'.$this->GetExportTableName().'.csv"><strong>'.URL_OUTBOX.'/'.$this->GetExportTableName().'.csv</strong></a><br />';
        $sMsg .= 'Sie können die Datei über rechts-klick und speichern unter lokal speichern. Bitte beachten Sie, dass die Datei UTF-8 Codiert ist.';
        $this->aMessages[] = $sMsg;

        return $bImportSucceeded;
    }

    /**
     * return complete and absolute file path to the export file we want to create.
     *
     * @return string
     */
    protected function GetAbsoluteFilePath()
    {
        return PATH_OUTBOX.'/'.$this->GetExportTableName().'.csv';
    }

    /**
     * @return string
     */
    protected function CreateCSV()
    {
        $sFile = $this->GetAbsoluteFilePath();
        if (file_exists($sFile)) {
            unlink($sFile);
        }
        $this->sCSVFilePath = $sFile;
        $fp = fopen($sFile, 'wb');
        $aFields = $this->GetFieldMapping();
        $this->WriteRow($fp, array_keys($aFields));
        $query = 'SELECT * FROM `'.$this->GetExportTableName().'`';
        $tRes = MySqlLegacySupport::getInstance()->query($query);
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
            unset($aRow['id']);
            $this->WriteRow($fp, array_values($aRow));
        }

        fclose($fp);

        return $sFile;
    }

    /**
     * @param resource $fp
     * @param mixed[] $aRow
     *
     * @return void
     */
    protected function WriteRow($fp, $aRow)
    {
        $sLine = '';
        foreach ($aRow as $sValue) {
            $sValue = str_replace(['"', "\n"], ['\\"', ' '], $sValue);
            $sLine .= '"'.$sValue.'";';
        }
        $sLine .= "\n";
        $sLine = utf8_decode($sLine);
        fwrite($fp, $sLine, strlen($sLine));
    }
}
