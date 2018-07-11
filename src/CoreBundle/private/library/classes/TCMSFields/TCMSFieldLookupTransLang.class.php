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
 * lookup.
/**/
class TCMSFieldLookupTransLang extends TCMSFieldLookup
{
    public function GetOptions()
    {
        $oGlobal = TGlobal::instance();
        $languageList = $oGlobal->oUser->oAccessManager->user->editLanguages->GetLanguageList();

        $this->options = array();
        $tblName = mb_substr($this->name, 0, -3);
        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $tblName);
        $sNameField = $oTableConf->GetNameColumn();

        if (!empty($languageList)) {
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($tblName).'` WHERE `id` IN ('.$languageList.') ORDER BY `'.MySqlLegacySupport::getInstance()->real_escape_string($sNameField).'`';
        } else {
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($tblName).'` ORDER BY `'.MySqlLegacySupport::getInstance()->real_escape_string($sNameField).'`';
        }
        $oTable = new TCMSRecordList('TCMSRecord', $tblName, $query);
        $this->allowEmptySelection = true; // add the "please choose" option
        while ($oRow = $oTable->Next()) {
            $this->options[$oRow->id] = $oRow->GetName();
        }
    }
}
