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

class TableMenuItemProvider implements MenuItemProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function createMenuItem(\TdbCmsMenuItem $menuItem): ?MenuItem
    {
        $tableConf = \TdbCmsTblConf::GetNewInstance();
        $loadSuccess = $tableConf->Load($menuItem->fieldTarget);

        if (false === $loadSuccess) {
            return null;
        }

        if (false === $this->isTableAccessAllowed($tableConf)) {
            return null;
        }

        return new MenuItem(
            $menuItem->id,
            $menuItem->fieldName,
            $menuItem->fieldIconFontCssClass,
            PATH_CMS_CONTROLLER."?pagedef=tablemanager&id={$tableConf->id}"
        );
    }

    private function isTableAccessAllowed(\TdbCmsTblConf $tableObject): bool
    {
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return false;
        }

        $isUserInTableUserGroup = $activeUser->oAccessManager->user->IsInGroups($tableObject->fieldCmsUsergroupId);
        $isEditAllowed = $activeUser->oAccessManager->HasEditPermission($tableObject->fieldName);
        $isShowAllReadonlyAllowed = $activeUser->oAccessManager->HasShowAllReadOnlyPermission($tableObject->fieldName);

        return true === $isUserInTableUserGroup && (true === $isEditAllowed || true === $isShowAllReadonlyAllowed);
    }
}
