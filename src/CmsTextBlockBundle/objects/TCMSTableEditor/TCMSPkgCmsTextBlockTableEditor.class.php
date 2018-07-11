<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSPkgCmsTextBlockTableEditor extends TCMSTableEditor
{
    /**
     * gets called after save if all posted data was valid
     * clear cache for linked portals and text fields containing the placeholder from the text block item.
     *
     * @param TIterator  $oFields    holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     */
    protected function PrepareDataForSave($postData)
    {
        $this->ClearPortalCache($postData);
        $this->ClearWysiwygTextFieldCache($postData);

        return parent::PrepareDataForSave($postData);
    }

    /**
     * Clear cache for linked portals.
     *
     * @param  $postData
     */
    protected function ClearPortalCache($postData)
    {
        $aNewPortalConnectionIdList = $postData['cms_portal_mlt'];
        $aOldPortalConnectionIdList = $this->oTable->getFieldCmsPortalIdList();
        foreach ($aOldPortalConnectionIdList as $sOldPortalId) {
            TCacheManager::PerformeTableChange('cms_portal', $sOldPortalId);
            unset($aNewPortalConnectionIdList[$sOldPortalId]);
        }
        foreach ($aNewPortalConnectionIdList as $sNewPortalId) {
            TCacheManager::PerformeTableChange('cms_portal', $sNewPortalId);
        }
    }

    /**
     * Clear cache for text fields containing the placeholder from the text block item.
     *
     * @param  $postData
     */
    protected function ClearWysiwygTextFieldCache($postData)
    {
        $sQuery = "SELECT `cms_field_conf`.`name` as sFieldName, `cms_tbl_conf`.`name` AS sTableName FROM `cms_field_conf`
                    INNER JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                    INNER JOIN `cms_field_type` ON `cms_field_type`.`id` = `cms_field_conf`.`cms_field_type_id`
                    WHERE `cms_field_type`.`constname` = 'CMSFIELD_WYSIWYG'
                    ";
        $oRes = MySqlLegacySupport::getInstance()->query($sQuery);
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($oRes)) {
            $sTableFieldQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($aRow['sTableName']).'`
                           WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($aRow['sTableName']).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($aRow['sFieldName'])."` LIKE '%".MySqlLegacySupport::getInstance()->real_escape_string('[{cmsblock_'.$postData['systemname'].'}]')."%'
                              OR `".MySqlLegacySupport::getInstance()->real_escape_string($aRow['sTableName']).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($aRow['sFieldName'])."` LIKE '%".MySqlLegacySupport::getInstance()->real_escape_string('[{cmsblock_'.$this->oTable->fieldSystemname.'}]')."%'";
            $oTableFieldRes = MySqlLegacySupport::getInstance()->query($sTableFieldQuery);
            while ($aTableFieldRow = MySqlLegacySupport::getInstance()->fetch_assoc($oTableFieldRes)) {
                TCacheManager::PerformeTableChange($aRow['sTableName'], $aTableFieldRow['id']);
            }
        }
    }
}
