<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

// RenderedDashboardModule represents the rendered content of a DashboardModuleInterface
final class RenderedDashboardModule
{
    public function __construct(public readonly string $content)
    {
    }
}