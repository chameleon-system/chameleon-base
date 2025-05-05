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
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

/**
 * manages the webpage list (links to the template engine interface).
 * /**/
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $portals = $securityHelper->getUser()?->getPortals();
        if (null === $portals) {
            $portals = [];
        }
        $portalRestriction = implode(
            ', ',
            array_map(static fn (string $portalId) => $portalId,
                array_keys($portals))
        );

        $sQuery = '';
        if (!$securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN)) {
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
}
