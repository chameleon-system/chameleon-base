<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsTextBlockList extends TPkgCmsTextBlockListAutoParent
{
    /**
     * Get list of all text block object that belong to the given portal.
     *
     * @static
     *
     * @param string $sPortalId
     *
     * @return TdbPkgCmsTextBlockList
     */
    public static function GetPortalTextBlockList($sPortalId = '')
    {
        if (!empty($sPortalId)) {
            $sQuery = "SELECT `pkg_cms_text_block`.* FROM `pkg_cms_text_block`
                       LEFT JOIN `pkg_cms_text_block_cms_portal_mlt` ON `pkg_cms_text_block_cms_portal_mlt`.`source_id` = `pkg_cms_text_block`.`id`
                           WHERE `pkg_cms_text_block_cms_portal_mlt`.`target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPortalId)."'";
            $oPortalTextBlockList = TdbPkgCmsTextBlockList::GetList($sQuery);
        } else {
            $oPortalTextBlockList = TdbPkgCmsTextBlockList::GetList();
        }

        return $oPortalTextBlockList;
    }

    /**
     * Get rendered text block list as array.
     *
     * @param int $iWidth set the width of text block item
     *
     * @return array
     */
    public function GetRenderedTextBlockArray($iWidth = 600)
    {
        $aPortalTextBlockArray = $this->RenderBlockListArrayItems($iWidth);

        return $aPortalTextBlockArray;
    }

    /**
     * Render list items and return is as array.
     *
     * @param int $iWidth set the width of text block item
     *
     * @return array
     */
    protected function RenderBlockListArrayItems($iWidth = 600)
    {
        $aPortalTextBlockArray = [];
        $this->GoToStart();
        while ($oTextBlock = $this->Next()) {
            $aPortalTextBlockArray['cmsblock_'.$oTextBlock->fieldSystemname] = $oTextBlock->Render('standard', 'Customer', ['iWidth' => $iWidth]);
        }

        return $aPortalTextBlockArray;
    }
}
