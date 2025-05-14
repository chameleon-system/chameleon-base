<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

class CustomMenuItemProvider implements MenuItemProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function createMenuItem(\TdbCmsMenuItem $menuItem): ?MenuItem
    {
        $customItem = new \TdbCmsMenuCustomItem($menuItem->fieldTarget);

        if (false === $this->isItemAccessAllowed($customItem)) {
            return null;
        }

        return new MenuItem(
            $menuItem->id,
            $menuItem->fieldName,
            $menuItem->fieldIconFontCssClass,
            $customItem->fieldUrl.'&_rmhist=true&_histid=0'
        );
    }

    private function isItemAccessAllowed(\TdbCmsMenuCustomItem $customItem): bool
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return false;
        }

        if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::ACCESS, $customItem)) {
            return false;
        }

        return true;
    }
}
