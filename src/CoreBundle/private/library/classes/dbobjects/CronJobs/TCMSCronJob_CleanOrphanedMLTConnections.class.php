<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSCronJob_CleanOrphanedMLTConnections extends TCMSCronJob
{
    /**
     * @deprecated since 6.3.0 - not used anymore
     */
    const MLT_DELETE_LOG_FILE = 'mlt_delete_log';

    protected function _ExecuteCron()
    {
        $sDeleteLog = '';

        //get all fields which represent a mlt connection
        $oFieldTypeList = TdbCmsFieldTypeList::GetList();
        $oFieldTypeList->AddFilterString("`base_type` = 'mlt'");
        $aFieldTypeIds = $oFieldTypeList->GetIdList();
        $oFieldList = TdbCmsFieldConfList::GetList();
        $aFieldTypeIds = TTools::MysqlRealEscapeArray($aFieldTypeIds);
        $oFieldList->AddFilterString("`cms_field_type_id` IN ('".implode("','", $aFieldTypeIds)."')");
        $aDeleteList = array();
        while ($oTmpField = $oFieldList->Next()) {
            $oField = $this->GetFieldObjectForCmsFieldConf($oTmpField);
            if (null !== $oField) {
                $sTableName = $oField->sTableName;
                $sForeignTableName = '';
                if (method_exists($oField, 'GetConnectedTableName')) {
                    $sForeignTableName = $oField->GetConnectedTableName();
                }
                $sMLTTableName = '';
                if (method_exists($oField, 'GetMLTTableName')) {
                    $sMLTTableName = $oField->GetMLTTableName();
                }
            }

            if (!empty($sTableName) && !empty($sForeignTableName) && !empty($sMLTTableName)) {
                $sQuery = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.* FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`
                            LEFT JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` AS sourcetable ON sourcetable.`id` = `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.`source_id`
                            LEFT JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'` AS targettable ON targettable.`id` = `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'`.`target_id`
                            WHERE targettable.`id` IS NULL OR sourcetable.`id` IS NULL
                          ';
                $rRes = MySqlLegacySupport::getInstance()->query($sQuery);
                $sError = MySqlLegacySupport::getInstance()->error();
                if ('' !== $sError) {
                    //error, so just do nothing
                } else {
                    while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
                        if (!isset($aDeleteList[$sMLTTableName])) {
                            $aDeleteList[$sMLTTableName] = array();
                        }
                        $aDeleteList[$sMLTTableName][] = array('s' => $aRow['source_id'], 't' => $aRow['target_id']);
                        $sDeleteQuery = 'DELETE FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName)."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['source_id'])."' AND `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['target_id'])."' LIMIT 1";
                        MySqlLegacySupport::getInstance()->query($sDeleteQuery);
                    }
                }
            }
        }

        if (count($aDeleteList) > 0) {
            foreach ($aDeleteList as $sTableName => $aDeletes) {
                $this->getCronjobLogger()->warning(
                    sprintf('Removed %s MLT orphaned mlt entries from %s.', \count($aDeletes), $sTableName),
                    [
                        'deletelist' => $aDeletes,
                    ]
                );
            }
        }
    }

    /**
     * @param TdbCmsFieldConf $oTmpField
     *
     * @return TCMSFieldLookupMultiselect
     */
    protected function GetFieldObjectForCmsFieldConf($oTmpField)
    {
        $oTableConf = $oTmpField->GetFieldCmsTblConf();
        $oField = $oTmpField->GetFieldObject();
        $oField->sTableName = $oTableConf->fieldName;
        $oField->oDefinition = $oTmpField;
        $oField->name = $oTmpField->sqlData['name'];

        return $oField;
    }
}
