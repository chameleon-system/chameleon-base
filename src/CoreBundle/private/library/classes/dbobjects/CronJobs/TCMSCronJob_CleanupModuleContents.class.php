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
 * deletes orphaned module contents that are leftovers of already deleted module instances.
 *
 * /**/
class TCMSCronJob_CleanupModuleContents extends TdbCmsCronjobs
{
    protected function _ExecuteCron()
    {
        // delete module instances of modules that are not installed anymore
        $oCmsTplModuleList = TdbCmsTplModuleList::GetList();
        $sIds = $oCmsTplModuleList->GetIdList('id', true);
        $sDeleteQuery = 'DELETE FROM `cms_tpl_module_instance` WHERE `cms_tpl_module_id` NOT IN ('.$sIds.')';
        MySqlLegacySupport::getInstance()->query($sDeleteQuery);

        // delete module contents where the module instance is missing
        $sFieldQuery = "SELECT * FROM `cms_field_conf` WHERE `name` = 'cms_tpl_module_instance_id'";
        $sFieldResult = MySqlLegacySupport::getInstance()->query($sFieldQuery);
        while ($aField = MySqlLegacySupport::getInstance()->fetch_assoc($sFieldResult)) {
            $sTableID = $aField['cms_tbl_conf_id'];
            if (!empty($sTableID)) {
                $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
                /** @var $oCmsTblConf TdbCmsTblConf */
                if ($oCmsTblConf->Load($sTableID)) {
                    $sTableSQLName = $oCmsTblConf->fieldName;
                    $databaseConnection = $this->getDatabaseConnection();
                    $quotedTableSqlName = $databaseConnection->quoteIdentifier($sTableSQLName);

                    $query = "SELECT * FROM $quotedTableSqlName WHERE `cms_tpl_module_instance_id` != ''";
                    $result = MySqlLegacySupport::getInstance()->query($query);
                    while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
                        $sCheckQuery = "SELECT * FROM `cms_tpl_module_instance` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($row['cms_tpl_module_instance_id'])."'";
                        $sCheckResult = MySqlLegacySupport::getInstance()->query($sCheckQuery);
                        if (0 == MySqlLegacySupport::getInstance()->num_rows($sCheckResult)) {
                            $sDeleteQuery = "DELETE FROM $quotedTableSqlName WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($row['id'])."'";
                            MySqlLegacySupport::getInstance()->query($sDeleteQuery);
                            if (_DEVELOPMENT_MODE) {
                                echo '<div>deleted: '.$sTableSQLName.' => '.$row['id'].'</div>';
                            }
                        }
                    }
                }
            }
        }
    }
}
