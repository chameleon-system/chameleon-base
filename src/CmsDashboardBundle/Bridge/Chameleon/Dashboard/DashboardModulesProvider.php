<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

final class DashboardModulesProvider
{
    /**
    * Array<DashboardModuleInterface>
    */
    private array $modules = [];

    // addDashboardModule is used by the compiler pass to add all tagged dashboard modules to this provider
    public function addDashboardModule(DashboardModuleInterface $module, string $id): void
    {
        $this->modules[$id] = $module;
    }

    /**
    * @return Array<ModuleDescription>
    */
    public function getAllModules(): array
    {
        $descriptions = [];
        foreach ($this->modules as $id => $service) {
            $descriptions[] = new ModuleDescription(
                id: $id,
                description: $service->description(),
                name: $service->name()
            );
        }

        return $descriptions;
    }

    /**
    * @return Array<DashboardModuleInterface>
    */
    public function getEnabledModules(): array
    {
        // TODO: get the configuration from somewhere
        // usually we want a list of ids and positions here
        // for now we just return all available modules for demo purposes

        return $this->modules;
    }
}