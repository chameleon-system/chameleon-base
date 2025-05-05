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
 * /**/
class TCMSFieldTablefieldnameExport extends TCMSFieldTablefieldname
{
    public function GetOptions()
    {
        $profileID = $this->oTableRow->sqlData['cms_export_profiles_id'];
        $query = "SELECT * FROM `cms_export_profiles` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($profileID)."'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        $row = MySqlLegacySupport::getInstance()->fetch_assoc($result);

        $tableID = $row['cms_tbl_conf_id'];

        if (!empty($tableID)) {
            $query2 = "SELECT * FROM `cms_tbl_conf` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableID)."'";
            $result2 = MySqlLegacySupport::getInstance()->query($query2);
            $tblConfRow = MySqlLegacySupport::getInstance()->fetch_assoc($result2);
            $tableName = $tblConfRow['name'];

            // we need to fetch the field translation...
            $query = "SELECT `id` FROM `cms_tbl_conf` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableName)."'";
            if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $oTableConf = new TCMSTableConf();
                /* @var $oTableConf TCMSTableConf */
                $oTableConf->Load($tmp['id']);

                $oFields = $oTableConf->GetFieldDefinitions();
                while ($oField = $oFields->Next()) {
                    $fieldTitleDecoded = $oField->sqlData['translation'];

                    $fieldTitle = $fieldTitleDecoded;
                    $this->options[$oField->sqlData['name']] = $fieldTitle;
                }
            }
        } else {
            $this->options[] = 'Sie müssen im Profil';
            $this->options[] = 'erst eine Tabelle zuweisen,';
            $this->options[] = 'bevor Sie Felder auswählen können';
        }
    }
}
