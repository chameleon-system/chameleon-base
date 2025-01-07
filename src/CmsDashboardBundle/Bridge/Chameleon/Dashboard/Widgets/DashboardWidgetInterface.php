<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;

interface DashboardWidgetInterface
{
    public function getTitle(): string;

    /**
     * @return array<array<WidgetDropdownItemDataModel>>
     */
    public function getDropdownItems(): array;

    public function getFooterIncludes(): array;

    public function getBodyHtml(): string;

    public function getFooterHtml(): string;

    public function getColorCssClass(): string;
}