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
 * allow mlt selection of fields. Target table is defined via sShowFieldsFromTable.
/**/
class TCMSFieldLookupMultiselectCheckboxesUnique extends TCMSFieldLookupMultiselectCheckboxes
{
    public function FetchMLTRecords()
    {
        $foreignTableName = $this->GetForeignTableName();
        $oTableConfig = new TCMSTableConf();
        /** @var $oTableConf TCMSTableConf */
        $oTableConfig->LoadFromField('name', $foreignTableName);

        $oTableList = $oTableConfig->GetListObject();

        $oTableList->sRestriction = null; // do not include the restriction - it is part of the parent table, not the mlt!

        $sFilterQuery = $oTableList->FilterQuery().$this->GetMLTRecordRestrictions();
        $sFilterQuery = $this->AddUsedMltConnectionsToRestrictQuery($sFilterQuery, $foreignTableName);
        $sFilterQueryOrderInfo = $oTableList->GetSortInfoAsString();
        if (!empty($sFilterQueryOrderInfo)) {
            $sFilterQuery .= ' ORDER BY '.$sFilterQueryOrderInfo;
        }
        $oUser = TCMSUser::GetActiveUser();
        /** @var $oMLTRecords TCMSRecordList */
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $foreignTableName).'List';
        $oMLTRecords = call_user_func(array($sClassName, 'GetList'), $sFilterQuery, null, false);
        $oMLTRecords->SetLanguage($oUser->GetCurrentEditLanguageID());

        return $oMLTRecords;
    }

    protected function GetUsedMltConnectionIds()
    {
        $aUsedIdList = array();
        $sMltTableName = $this->GetMLTTableName();
        $Select = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($sMltTableName).'`.`target_id` as restricted_id FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                    INNER JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($sMltTableName).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($sMltTableName).'`.`source_id` = `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`id`
                         WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName)."`.`id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->recordId)."'";
        $oRes = MySqlLegacySupport::getInstance()->query($Select);
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($oRes)) {
            $aUsedIdList[] = $aRow['restricted_id'];
        }

        return $aUsedIdList;
    }

    protected function AddUsedMltConnectionsToRestrictQuery($sFilterQuery, $sForeignTableName)
    {
        $aUsedIdList = $this->GetUsedMltConnectionIds();
        if (count($aUsedIdList) > 0) {
            foreach ($aUsedIdList as $sUsedId) {
                $sFilterQuery .= ' AND `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName)."`.`id` != '".MySqlLegacySupport::getInstance()->real_escape_string($sUsedId)."' ";
            }
        }

        return $sFilterQuery;
    }
}
