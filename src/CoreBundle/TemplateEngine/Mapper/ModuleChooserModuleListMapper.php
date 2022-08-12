<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\TemplateEngine\Mapper;

use AbstractViewMapper;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;
use TGlobal;

class ModuleChooserModuleListMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('moduleList', 'TdbCmsTplModuleList');
        $oRequirements->NeedsSourceObject('aPermittedModules', 'array', null, true);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        /** @var array $permittedModules */
        $permittedModules = $oVisitor->GetSourceObject('aPermittedModules');

        /** @var \TdbCmsTplModuleList $moduleList */
        $moduleList = $oVisitor->GetSourceObject('moduleList');

        $modules = array();
        $moduleList->GoToStart();
        $moduleList->bAllowItemCache = true;
        while ($moduleObject = $moduleList->Next()) {
            if ('' === $moduleObject->fieldName) {
                continue;
            }
            if (is_array($permittedModules) && false === isset($permittedModules[$moduleObject->fieldClassname])) {
                continue;
            }
            $module = array(
                'name' => $moduleObject->fieldName,
                'id' => $moduleObject->id,
                'icon' => $moduleObject->fieldIconFontCssClass,
                'views' => array(),
            );
            $moduleObject->aPermittedViews = $permittedModules[$moduleObject->fieldClassname] ?? null;
            /** @var \TIterator<string> $views */
            $views = $moduleObject->GetViews();
            if (0 === $views->Length()) {
                continue;
            }
            $viewMapping = $moduleObject->GetViewMapping();

            $views->GoToStart();
            while ($viewName = $views->Next()) {
                $displayName = (is_array($viewMapping) && isset($viewMapping[$viewName])) ? $viewMapping[$viewName] : $viewName;
                $module['views'][] = array(
                    'systemName' => $viewName,
                    'displayName' => $displayName,
                );
            }
            usort($module['views'], function ($element1, $element2) {
                return $element1['displayName'] > $element2['displayName'] ? 1 : 0;
            });

            $modules[] = $module;
        }

        $data = array(
            'iconPath' => TGlobal::GetStaticURLToWebLib('/'),
            'modules' => $modules,
        );
        $oVisitor->SetMappedValueFromArray($data);
    }
}
