<?php

namespace ChameleonSystem\CoreBundle\TemplateEngine\Mapper;

class ModuleChooserModuleListMapper extends \AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('moduleList', 'TdbCmsTplModuleList');
        $oRequirements->NeedsSourceObject('aPermittedModules', 'array', null, true);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $permittedModules = $oVisitor->GetSourceObject('aPermittedModules');

        /** @var \TdbCmsTplModuleList $moduleList */
        $moduleList = $oVisitor->GetSourceObject('moduleList');

        $modules = $this->getModules($moduleList, $permittedModules);

        $data = [
            'iconPath' => \TGlobal::GetStaticURLToWebLib('/'),
            'modules' => $modules,
        ];
        $oVisitor->SetMappedValueFromArray($data);
    }

    private function getModules(\TdbCmsTplModuleList $moduleList, ?array $permittedModules): array
    {
        $modules = [];
        $moduleList->GoToStart();
        $moduleList->bAllowItemCache = true;
        while ($moduleObject = $moduleList->Next()) {
            if ('' === $moduleObject->fieldName) {
                continue;
            }
            if (is_array($permittedModules) && false === isset($permittedModules[$moduleObject->fieldClassname])) {
                continue;
            }
            $modules[] = $this->getModuleData($moduleObject, $permittedModules);
        }

        return $modules;
    }

    private function getModuleData(\TdbCmsTplModule $moduleObject, ?array $permittedModules): array
    {
        $module = [
            'name' => $moduleObject->fieldName,
            'id' => $moduleObject->id,
            'views' => [],
        ];
        $moduleObject->aPermittedViews = $permittedModules[$moduleObject->fieldClassname] ?? [];
        $views = $moduleObject->GetViews();
        if (null !== $views && 0 !== $views->Length()) {
            $module['views'] = $this->getModuleViews($views, $moduleObject->GetViewMapping());
        }

        return $module;
    }

    private function getModuleViews($views, $viewMapping): array
    {
        $moduleViews = [];
        $views->GoToStart();
        while ($viewName = $views->Next()) {
            $displayName = (is_array($viewMapping) && isset($viewMapping[$viewName])) ? $viewMapping[$viewName] : $viewName;
            $moduleViews[] = [
                'systemName' => $viewName,
                'displayName' => $displayName,
            ];
        }
        usort($moduleViews, function ($element1, $element2) {
            return $element1['displayName'] > $element2['displayName'] ? 1 : 0;
        });

        return $moduleViews;
    }
}
