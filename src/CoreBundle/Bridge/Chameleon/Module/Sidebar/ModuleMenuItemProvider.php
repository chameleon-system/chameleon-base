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
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return false;
        }

        return true === $activeUser->oAccessManager->user->IsInGroups($cmsModule->fieldCmsUsergroupId);
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

        return $url;
    }
}
