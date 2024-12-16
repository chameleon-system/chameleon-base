<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

// DashboardModuleInterface is implemented by all Dashboard Modules
interface DashboardModuleInterface
{
    // render() returns the raw rendered html to display on the dashboard
    public function render(): string;

    // name() returns the name of the module. It is used to represent the module in selection lists for example
    public function name(): string;

    // description() returns a concise description of what kind of info this module provides when rendered
    public function description(): string;
}