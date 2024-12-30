<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Module;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\DashboardModulesProvider;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\RenderedDashboardModule;

// Dashboard is the main module that renders all dashboard modules inside the dashboard page in the backend
final class Dashboard extends \MTPkgViewRendererAbstractModuleMapper
{
    public function __construct(private readonly DashboardModulesProvider $provider)
    {
        parent::__construct();
    }

    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $oVisitor->SetMappedValue('greeting', 'Hello World from Dashboard');
        $oVisitor->SetMappedValue('availableModules', $this->provider->getAllModules());

        $enabledModules = $this->provider->getEnabledModules();
        $renderedModules = [];
        foreach ($enabledModules as $module) {
            $renderedModules[] = new RenderedDashboardModule(content: $module->render());
        }

        $oVisitor->SetMappedValue('renderedModules', $renderedModules);
    }
}
