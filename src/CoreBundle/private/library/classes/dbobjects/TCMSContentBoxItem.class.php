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
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

/**
 * holds one Menu (category) for the CMS, including icon, title, and menu items.
 *
 * @deprecated since 6.3.0 - only used for deprecated classic main menu
/**/
class TCMSContentBoxItem extends TAdbCmsContentBox
{
    /**
     * menu items iterator of TCMSMenuItem.
     *
     * @var TIterator
     */
    public $oMenuItems = null;

    /**
     * Draw the menu header (outputs HTML).
     */
    public function DrawBoxHeader()
    {
        TCMSRender::DrawBoxHeader($this->sqlData['name'], '');
    }

    /**
     * Draw the menu footer (outputs HTML).
     */
    public function DrawBoxFooter()
    {
        TCMSRender::DrawBoxFooter();
    }

    public function loadMenuItems()
    {
        if (!is_null($this->id)) {
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
                return ;
            }
            $aMenuItemsTemp = array();

            $this->oMenuItems = new TIterator();
            // fetch tables
            $query = "SELECT *
                    FROM `cms_tbl_conf`
                   WHERE `cms_content_box_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."' ORDER BY `translation`";
            $oTableList = TdbCmsTblConfList::GetList($query, $this->iLanguageId);
            while ($oTableObj = $oTableList->Next()) {
                if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $oTableObj->id)) {
                    continue;
                }
                $oTableItem = new TCMSMenuItem_Table();
                $oTableItem->SetData($oTableObj->sqlData);
                if (array_key_exists($oTableObj->sqlData['translation'], $aMenuItemsTemp)) {
                    $oTableObj->sqlData['translation'] = $oTableObj->sqlData['translation'].'1';
                }
                $aMenuItemsTemp[$oTableObj->sqlData['translation']] = $oTableItem;
            }

            // fetch modules
            $query = "SELECT *
                    FROM `cms_module`
                   WHERE `cms_content_box_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                     AND `active` = '1'
                ORDER BY `name`";

            $oCMSModuleList = TdbCmsModuleList::GetList($query, $this->iLanguageId);
            while ($oCMSModule = $oCMSModuleList->Next()) {
                if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::ACCESS, $oCMSModule)) {
                    continue;
                }
                $oMenuItem = new TCMSMenuItem_Module();
                $oMenuItem->SetData($oCMSModule->sqlData);

                if (array_key_exists($oCMSModule->sqlData['name'], $aMenuItemsTemp)) {
                    $oCMSModule->sqlData['name'] = $oCMSModule->sqlData['name'].'1';
                }
                $aMenuItemsTemp[$oCMSModule->sqlData['name']] = $oMenuItem;
            }

            ksort($aMenuItemsTemp);
            foreach ($aMenuItemsTemp as $key => $oMenuItem) {
                $this->oMenuItems->AddItem($oMenuItem);
            }
        }
    }
}
