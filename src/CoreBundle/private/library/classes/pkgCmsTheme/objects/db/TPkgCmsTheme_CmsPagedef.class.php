<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsTheme_CmsPagedef extends TPkgCmsTheme_CmsPagedefAutoParent
{
    /**
     * {@inheritdoc}
     */
    protected function LoadPageDefVars()
    {
        if (false === parent::LoadPageDefVars()) {
            return false;
        }

        $query = "SELECT `cms_tpl_page_cms_master_pagedef_spot`.*,
                         `cms_master_pagedef_spot`.`name` AS spotname
                  FROM `cms_tpl_page_cms_master_pagedef_spot`
                  INNER JOIN `cms_master_pagedef_spot` ON `cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id` = `cms_master_pagedef_spot`.`id`
                  INNER JOIN `pkg_cms_theme_block` ON `pkg_cms_theme_block`.`id` = `cms_master_pagedef_spot`.`pkg_cms_theme_block_id`
                  WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                  AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` != ''
                  AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` != '0'
             ";
        $oCmsTplPageCmsMasterPagedefSpotList = &TdbCmsTplPageCmsMasterPagedefSpotList::GetList($query);
        while ($oCmsTplPageCmsMasterPagedefSpot = $oCmsTplPageCmsMasterPagedefSpotList->Next()) {
            $this->UpdateModule($oCmsTplPageCmsMasterPagedefSpot->sqlData['spotname'], $oCmsTplPageCmsMasterPagedefSpot->fieldModel, $oCmsTplPageCmsMasterPagedefSpot->fieldView, $oCmsTplPageCmsMasterPagedefSpot->fieldCmsTplModuleInstanceId);
        }

        return true;
    }
}
