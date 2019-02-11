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
 * manages the webpage list (links to the template engine interface).
/**/
class TCMSListManagerWebpages extends TCMSListManagerFullGroupTable
{
    /**
     * overwrite the function column to use a class specific callback.
     */
    public function _AddFunctionColumn()
    {
        ++$this->columnCount;
        $sTranslatedField = TGlobal::Translate('chameleon_system_core.list.column_name_actions');
        $this->tableObj->AddHeaderField(array('id' => $sTranslatedField.'&nbsp;&nbsp;'), 'right', null, 1, false, 100);
        $this->tableObj->AddColumn('id', 'left', array($this, 'CallBackFunctionBlock'), null, 1);
    }

    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'OpenTemplateEngine';
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        $userIsInWebsiteEditGroup = false;
        $userHasWebsiteEditRight = false;
        $userHasNaviEditRight = false;

        $oGlobal = TGlobal::instance();
        if (TGlobal::CMSUserDefined()) {
            $query = "SELECT `id` FROM `cms_usergroup` WHERE `internal_identifier` = 'website_editor'";
            $result = MySqlLegacySupport::getInstance()->query($query);

            if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                $userGroupRow = MySqlLegacySupport::getInstance()->fetch_assoc($result);
                // check if user is in group "website_editor"
                $userIsInWebsiteEditGroup = $oGlobal->oUser->oAccessManager->user->IsInGroups($userGroupRow['id']);
                $userHasWebsiteEditRight = $oGlobal->oUser->oAccessManager->HasEditPermission('cms_tpl_page');
                $userHasNaviEditRight = $oGlobal->oUser->oAccessManager->HasEditPermission('cms_tree');
            }
        }

        if ($userIsInWebsiteEditGroup && $userHasWebsiteEditRight && $userHasNaviEditRight) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_core.action.pages_regenerate_page_path');
            $oMenuItem->sIcon = 'fas fa-sync';
            $js = "document.location.href='".PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'tablemanager', 'id' => $this->oTableConf->id, 'module_fnc' => array('contentmodule' => 'ClearNaviCache'), '_rmhist' => 'false'))."';";
            $oMenuItem->sOnClick = $js;
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }

    /**
     * @param string $id
     * @param array  $row
     *
     * @return array
     */
    protected function getRowFunctionItems($id, $row)
    {
        $aItems = parent::getRowFunctionItems($id, $row);
        // drop copy item
        unset($aItems['copy']);

        $oGlobal = TGlobal::instance();

        if ($oGlobal->oUser->oAccessManager->PermitFunction('cms_page_property')) {
            $aItems['pageConfig'] = '<a title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.page_settings')).'" href="javascript:document.cmsform.id.value=\''.$row['id'].'\';document.cmsform.submit();"><i class="fas fa-cog"></i></a>';
        }

        return $aItems;
    }
}
