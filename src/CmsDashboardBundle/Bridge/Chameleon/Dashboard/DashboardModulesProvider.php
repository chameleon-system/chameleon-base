<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardWidgetInterface;

final class DashboardModulesProvider
{
    /**
    * Array<DashboardModuleInterface>
    */
    private array $widgets = [];

    /**
     * Is used by the compiler pass to add all tagged dashboard widgets to this provider.
     **/
    public function addDashboardWidget(DashboardWidgetInterface $widget, string $id, string $collection = 'default', int $priority = 0): void
    {
        $this->widgets[$collection][] = [
            'id' => $id,
            'widget' => $widget,
            'priority' => $priority,
        ];
    }

    /**
    * @return Array<DashboardWidgetInterface>
    */
    public function getWidgetCollections(string $collection = 'default'): array
    {
        return $this->widgets;

        /*if (!isset($this->widgets[$collection])) {
            return [];
        }

        usort($this->widgets[$collection], function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return array_column($this->widgets[$collection], 'widget');*/
    }
}