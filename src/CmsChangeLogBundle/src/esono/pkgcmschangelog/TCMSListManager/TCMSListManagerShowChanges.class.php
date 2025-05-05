<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerShowChanges extends TCMSListManagerShowChangesAutoParent
{
    /**
     * @return void
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        if ($this->oTableConf->fieldChangelogActive) {
            $aParam = [];
            $aParam['pagedef'] = 'tablemanager';
            $aParam['id'] = TTools::GetCMSTableId('pkg_cms_changelog_set');
            $aParam['sRestrictionField'] = 'cms_tbl_conf';
            $aParam['sRestriction'] = $this->oTableConf->id;

            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sItemKey = 'getdisplayvalue';
            $oMenuItem->sDisplayName = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.action.show_changes');
            $oMenuItem->sIcon = 'far fa-edit';
            $oMenuItem->href = '?'.TTools::GetArrayAsURL($aParam);
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }
}
