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

class ModuleMenuItemProvider implements MenuItemProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function createMenuItem(\TdbCmsMenuItem $menuItem): ?MenuItem
    {
        $cmsModule = new \TdbCmsModule($menuItem->fieldTarget);

        if (false === $this->isModuleAccessAllowed($cmsModule)) {
            return null;
        }

        return new MenuItem(
            $menuItem->id,
            $menuItem->fieldName,
            $menuItem->fieldIconFontCssClass,
            $this->getModuleTargetUrl($cmsModule)
        );
    }

    private function isModuleAccessAllowed(\TdbCmsModule $cmsModule): bool
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return false;
        }

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::ACCESS, $cmsModule);
    }

    private function getModuleTargetUrl(\TdbCmsModule $cmsModule): string
    {
        $url = PATH_CMS_CONTROLLER.'?pagedef='.$cmsModule->fieldModule;
        if ('' !== $cmsModule->fieldParameter) {
            $url .= '&'.$cmsModule->fieldParameter;
        }
        if ('' !== $cmsModule->fieldModuleLocation) {
            $url .= '&_pagedefType='.$cmsModule->fieldModuleLocation;
        }

        $url .= '&_rmhist=true&_histid=0';

        return $url;
    }
}
