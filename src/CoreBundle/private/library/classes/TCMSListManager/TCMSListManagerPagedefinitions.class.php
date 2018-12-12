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
class TCMSListManagerPagedefinitions extends TCMSListManagerFullGroupTable
{
    /**
     * change the portal restriction so that we can see all portals that belong to
     * the current user, and those marked as "default).
     *
     * @return string
     */
    public function GetPortalRestriction()
    {
        $oGlobal = TGlobal::instance();
        if ($oGlobal->oUser->oAccessManager->user->portals->hasNoPortals) {
            return false;
        } // exit if the field does not exist

        $sQuery = '';
        $portalRestriction = $oGlobal->oUser->oAccessManager->user->portals->PortalList();
        if (!$oGlobal->oUser->oAccessManager->user->roles->IsInRole('cms_admin')) {
            $sQuery = " (`cms_portal`.`id` IN ({$portalRestriction}) OR `cms_master_pagedef`.`restrict_to_portals`='0')";
        }

        return $sQuery;
    }

    protected function AddRowPrefixFields()
    {
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $this->oMenuItems->RemoveItem('sItemKey', 'deleteall');
        $this->oMenuItems->RemoveItem('sItemKey', 'edittableconf');
    }

    /**
     * {@inheritdoc}
     */
    protected function usesManagedTables(): bool
    {
        return false;
    }
}
