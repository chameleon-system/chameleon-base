<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsTheme_CmsMasterPagedef extends TPkgCmsTheme_CmsMasterPagedefAutoParent
{
    /**
     * Add spots from theme_blocks, dynamic spots need to be initialised later.
     *
     * @param array $aSpots
     *
     * @return array
     */
    protected function AddAdditionalSpots($aSpots)
    {
        $sQuery = "SELECT `cms_master_pagedef_spot`.* FROM `cms_master_pagedef_spot`
                                    INNER JOIN `pkg_cms_theme_block` ON `pkg_cms_theme_block`.`id` = `cms_master_pagedef_spot`.`pkg_cms_theme_block_id`
                                    INNER JOIN `cms_master_pagedef_pkg_cms_theme_block_mlt` ON `cms_master_pagedef_pkg_cms_theme_block_mlt`.`target_id` = `pkg_cms_theme_block`.`id`
                                    WHERE `cms_master_pagedef_pkg_cms_theme_block_mlt`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                        ";
        $oSpotsBlock = TdbCmsMasterPagedefSpotList::GetList($sQuery);
        while ($oSpotBlock = $oSpotsBlock->Next()) { /* @var $oSpot TCMSMasterPagedefSpot */
            $aSpots[$oSpotBlock->sName] = $oSpotBlock;
        }

        return $aSpots;
    }
}
