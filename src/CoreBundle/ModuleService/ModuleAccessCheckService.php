<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\ModuleService;

class ModuleAccessCheckService implements ModuleAccessCheckServiceInterface
{
    public function checkAccess(\TdbCmsUser $user, \TModelBase $module): bool
    {
        if (false === $module->checkAccessRightsOnTable()) {
            return false;
        }

        $allowedGroups = $this->getModuleGroups($module);

        if (0 === \count($allowedGroups)) {
            return true;
        }

        return true === $user->oAccessManager->user->IsInGroups($allowedGroups);
    }

    private function getModuleGroups(\TModelBase $modelBase): array
    {
        $moduleModel = $modelBase->aModuleConfig['model'] ?? null;

        if (null === $moduleModel || '' === $moduleModel) {
            return [];
        }

        $tdbModule = \TdbCmsTplModule::GetNewInstance();

        if (false === $tdbModule->LoadFromField('classname', $moduleModel)) {
            return [];
        }

        return $tdbModule->GetFieldCmsUsergroupIdList();
    }
}
