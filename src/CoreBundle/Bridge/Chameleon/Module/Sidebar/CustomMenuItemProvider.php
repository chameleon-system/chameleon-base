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
            $menuItem->fieldName,
            $menuItem->fieldIconFontCssClass,
            $customItem->fieldUrl
        );
    }

    private function isItemAccessAllowed(\TdbCmsMenuCustomItem $customItem): bool
    {
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return false;
        }

        $rightList = $customItem->GetFieldCmsRightList();
        while (false !== $right = $rightList->Next()) {
            if (false === $activeUser->oAccessManager->PermitFunction($right->fieldName)) {
                return false;
            }
        }

        return true;
    }
}
