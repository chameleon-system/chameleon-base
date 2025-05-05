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

/**
 * manages the webpage list (links to the template engine interface).
 * /**/
class TCMSListManagerWebpages extends TCMSListManagerFullGroupTable
{
    /**
     * overwrite the function column to use a class specific callback.
     */
    public function _AddFunctionColumn()
    {
        ++$this->columnCount;
        $sTranslatedField = ServiceLocator::get('translator')->trans('chameleon_system_core.list.column_name_actions');
        $this->tableObj->AddHeaderField(['id' => $sTranslatedField.'&nbsp;&nbsp;'], 'right', null, 1, false, 100);
        $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackFunctionBlock'], null, 1);
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

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $userHasWebsiteEditRight = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->fieldName);
        $userHasNaviEditRight = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, 'cms_tree');

        if ($userHasWebsiteEditRight && $userHasNaviEditRight) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.pages_regenerate_page_path');
            $oMenuItem->sIcon = 'fas fa-sync';
            $js = "document.location.href='".PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(['pagedef' => 'tablemanager', 'id' => $this->oTableConf->id, 'module_fnc' => ['contentmodule' => 'ClearNaviCache'], '_rmhist' => 'false'])."';";
            $oMenuItem->sOnClick = $js;
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }

    /**
     * @param string $id
     * @param array $row
     *
     * @return array
     */
    protected function getRowFunctionItems($id, $row)
    {
        $aItems = parent::getRowFunctionItems($id, $row);
        // drop copy item
        unset($aItems['copy']);

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if ($securityHelper->isGranted('CMS_RIGHT_CMS_PAGE_PROPERTY')) {
            $aItems['pageConfig'] = '<a title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list.page_settings')).'" href="javascript:document.cmsform.id.value=\''.$row['id'].'\';document.cmsform.submit();"><i class="fas fa-cog"></i></a>';
        }

        return $aItems;
    }
}
