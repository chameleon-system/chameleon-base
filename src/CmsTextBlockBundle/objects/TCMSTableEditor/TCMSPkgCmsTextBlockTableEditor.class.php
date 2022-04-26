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
use esono\pkgCmsCache\CacheInterface;

/**
 * @property TdbPkgCmsTextBlock $oTable
 */
class TCMSPkgCmsTextBlockTableEditor extends TCMSTableEditor
{
    /**
     * Gets called after save if all posted data was valid.
     * Clear cache for linked portals and text fields containing the placeholder from the text block item.
     *
     * {@inheritDoc}
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
     * @param array $postData
     *
     * @return void
     */
    protected function ClearPortalCache($postData)
    {
        $aNewPortalConnectionIdList = $postData['cms_portal_mlt'];
        $aOldPortalConnectionIdList = $this->oTable->getFieldCmsPortalIdList();

        $cacheService = $this->getCacheService();

        foreach ($aOldPortalConnectionIdList as $sOldPortalId) {
            $cacheService->callTrigger('cms_portal', $sOldPortalId);
            unset($aNewPortalConnectionIdList[$sOldPortalId]);
        }
        foreach ($aNewPortalConnectionIdList as $sNewPortalId) {
            $cacheService->callTrigger('cms_portal', $sNewPortalId);
        }
    }

    /**
     * Clear cache for text fields containing the placeholder from the text block item.
     *
     * @param array $postData
     *
     * @return void
     */
    protected function ClearWysiwygTextFieldCache($postData)
    {
        $cacheService = $this->getCacheService();

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
                $cacheService->callTrigger($aRow['sTableName'], $aTableFieldRow['id']);
            }
        }
    }

    private function getCacheService(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }
}
