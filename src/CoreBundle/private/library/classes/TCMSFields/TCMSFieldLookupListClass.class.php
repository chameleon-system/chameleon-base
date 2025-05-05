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
 * /**/
class TCMSFieldLookupListClass extends TCMSFieldLookup
{
    protected function GetOptionsQuery()
    {
        if ('_id' == mb_substr($this->name, -3)) {
            $tblName = mb_substr($this->name, 0, -3);
        } else {
            $tblName = $this->name;
        }

        // check if the field has a counter and fix the lookup table name
        if (is_numeric(mb_substr($tblName, -1))) {
            $tblName = mb_substr($tblName, 0, -2);
        }

        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $tblName);
        $sNameField = $oTableConf->GetNameColumn();

        $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($tblName)."` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."' ORDER BY `".MySqlLegacySupport::getInstance()->real_escape_string($sNameField).'`';

        return $query;
    }
}
