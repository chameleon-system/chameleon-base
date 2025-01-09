<?php

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\ListManager;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use TCMSListManagerFullGroupTable;
use TCMSTableEditorMenuItem;

class RecordFieldsTableListView extends TCMSListManagerFullGroupTable
{
    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
            return;
        }

        $this->addAddSelectedAsListFieldsButton();
        $this->addAddSelectedAsSortFieldsButton();
    }

    protected function addAddSelectedAsListFieldsButton(): void
    {
        $sFormName = 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'add_selected_as_list_fields';
        $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.list.add_selected_as_list_fields'));

        $oMenuItem->sIcon = 'fas fa-list-alt';
        $oMenuItem->setButtonStyle('btn-info');
        $oMenuItem->sOnClick = "addSelectedAsListFields('{$sFormName}');";
        $this->oMenuItems->AddItem($oMenuItem);
    }

    protected function addAddSelectedAsSortFieldsButton(): void
    {
        $sFormName = 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'add_selected_as_sort_fields';
        $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.list.add_selected_as_sort_fields'));

        $oMenuItem->sIcon = 'fas fa-sort';
        $oMenuItem->setButtonStyle('btn-info');
        $oMenuItem->sOnClick = "addSelectedAsSortFields('{$sFormName}');";
        $this->oMenuItems->AddItem($oMenuItem);
    }
}
