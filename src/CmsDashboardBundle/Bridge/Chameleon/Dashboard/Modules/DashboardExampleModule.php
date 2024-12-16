<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Modules;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\DashboardModuleInterface;

final class DashboardExampleModule implements DashboardModuleInterface
{
    public function render(): string
    {
        return "<h3>Hallo, ich bin ein Beispiel Modul<h3>";
    }

    public function name(): string
    {
        return "Beispiel Modul";
    }

    public function description(): string
    {
        return "Ein Modul, das als Beispiel für die Implementierung von Dashboard Modulen dient";
    }
}