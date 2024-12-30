<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

// RenderedDashboardModule represents the rendered content of a DashboardModuleInterface
final readonly class RenderedDashboardModule
{
    public function __construct(public string $content)
    {
    }
}
