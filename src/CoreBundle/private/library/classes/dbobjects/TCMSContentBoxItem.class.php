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

    /**
     * load the menu items.
     *
     * @deprecated since 6.2.0 - use loadMenuItems() instead.
     */
    protected function _loadMenuItems()
    {
        $this->loadMenuItems();
    }

    public function loadMenuItems()
    {
        if (!is_null($this->id)) {
            $activeUser = TCMSUser::GetActiveUser();
            if (null === $activeUser) {
                return;
            }
            $aMenuItemsTemp = array();

            $this->oMenuItems = new TIterator();
            // fetch tables
            $query = "SELECT *
                    FROM `cms_tbl_conf`
                   WHERE `cms_content_box_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."' ORDER BY `translation`";
            $oTableList = TdbCmsTblConfList::GetList($query, $this->iLanguageId);
            while ($oTableObj = $oTableList->Next()) {
                $tableInUserGroup = $activeUser->oAccessManager->user->IsInGroups($oTableObj->fieldCmsUsergroupId);
                $bRightAllowEdit = $activeUser->oAccessManager->HasEditPermission($oTableObj->fieldName);
                $bRightShowAllReadOnly = $activeUser->oAccessManager->HasShowAllReadOnlyPermission($oTableObj->fieldName);

                if ($tableInUserGroup && ($bRightAllowEdit || $bRightShowAllReadOnly)) {
                    $oTableItem = new TCMSMenuItem_Table();
                    $oTableItem->SetData($oTableObj->sqlData);
                    if (array_key_exists($oTableObj->sqlData['translation'], $aMenuItemsTemp)) {
                        $oTableObj->sqlData['translation'] = $oTableObj->sqlData['translation'].'1';
                    }
                    $aMenuItemsTemp[$oTableObj->sqlData['translation']] = $oTableItem;
                }
            }

            // fetch modules
            $query = "SELECT *
                    FROM `cms_module`
                   WHERE `cms_content_box_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                     AND `active` = '1'
                ORDER BY `name`";

            $oCMSModuleList = TdbCmsModuleList::GetList($query, $this->iLanguageId);
            while ($oCMSModule = $oCMSModuleList->Next()) {
                $tableInUserGroup = $activeUser->oAccessManager->user->IsInGroups($oCMSModule->fieldCmsUsergroupId);
                if ($tableInUserGroup) {
                    $oMenuItem = new TCMSMenuItem_Module();
                    $oMenuItem->SetData($oCMSModule->sqlData);

                    if (array_key_exists($oCMSModule->sqlData['name'], $aMenuItemsTemp)) {
                        $oCMSModule->sqlData['name'] = $oCMSModule->sqlData['name'].'1';
                    }
                    $aMenuItemsTemp[$oCMSModule->sqlData['name']] = $oMenuItem;
                }
            }

            ksort($aMenuItemsTemp);
            foreach ($aMenuItemsTemp as $key => $oMenuItem) {
                $this->oMenuItems->AddItem($oMenuItem);
            }
        }
    }
}
