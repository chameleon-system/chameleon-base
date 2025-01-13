<?php

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\ListManager;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

class RecordFieldsTableListView extends \TCMSListManagerFullGroupTable
{
    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
        if (false === $tableInUserGroup) {
            return;
        }

        $this->addButtonExportData($securityHelper);
        $this->addAddSelectedAsListFieldsButton($securityHelper);
        $this->addAddSelectedAsSortFieldsButton($securityHelper);
        $this->addButtonDeleteAll($securityHelper);
    }

    protected function addAddSelectedAsListFieldsButton(SecurityHelperAccess $securityHelper): void
    {
        if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
            return;
        }

        $sFormName = 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
        $oMenuItem = new \TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'add_selected_as_list_fields';
        $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.list.add_selected_as_list_fields'));

        $oMenuItem->sIcon = 'fas fa-list-alt';
        $oMenuItem->setButtonStyle('btn-info');
        $oMenuItem->sOnClick = "addSelectedAsListFields('{$sFormName}');";
        $this->oMenuItems->AddItem($oMenuItem);
    }

    protected function addAddSelectedAsSortFieldsButton(SecurityHelperAccess $securityHelper): void
    {
        if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
            return;
        }

        $sFormName = 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
        $oMenuItem = new \TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'add_selected_as_sort_fields';
        $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.list.add_selected_as_sort_fields'));

        $oMenuItem->sIcon = 'fas fa-sort';
        $oMenuItem->setButtonStyle('btn-info');
        $oMenuItem->sOnClick = "addSelectedAsSortFields('{$sFormName}');";
        $this->oMenuItems->AddItem($oMenuItem);
    }
}
