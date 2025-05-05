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
            PATH_CMS_CONTROLLER."?pagedef=tablemanager&id={$tableConf->id}",
            $tableConf->id
        );
    }

    private function isTableAccessAllowed(\TdbCmsTblConf $tableObject): bool
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return false;
        }

        if (true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $tableObject->fieldName)) {
            return true;
        }

        if (true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $tableObject->fieldName)) {
            return true;
        }

        return false;
    }
}
