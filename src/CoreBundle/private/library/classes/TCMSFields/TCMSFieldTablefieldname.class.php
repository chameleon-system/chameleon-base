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
 * field name in a table.
/**/
class TCMSFieldTablefieldname extends TCMSFieldOption
{
    public function GetOptions()
    {
        // use the field name to make a lookup of the right table
        if (stristr($this->name, '_cmsfieldname')) {
            $tableName = mb_substr($this->name, 0, -13);
        } else {
            $tableName = $this->name;
        }

        // we need to fetch the field translation...
        $query = "SELECT id FROM `cms_tbl_conf` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableName)."'";
        if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $oTableConf = new TCMSTableConf();
            /** @var $oTableConf TCMSTableConf */
            $oTableConf->Load($tmp['id']);

            $oFields = $oTableConf->GetFieldDefinitions();
            while ($oField = $oFields->Next()) {
                $fieldTitleDecoded = $oField->sqlData['translation'];

                $fieldTitle = $fieldTitleDecoded;
                $this->options[$oField->sqlData['name']] = $fieldTitle;
            }
        }
    }
}
