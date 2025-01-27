<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;

interface DashboardWidgetInterface
{
    public function getTitle(): string;

    public function getWidgetId(): string;

    /**
     * @return array<array<WidgetDropdownItemDataModel>>
     */
    public function getDropdownItems(): array;

    public function showWidget(): bool;

    public function useWidgetContainerTemplate(): bool;

    public function getFooterIncludes(): array;

    public function getBodyHtml(bool $forceCacheReload = false): string;

    public function getFooterHtml(): string;

    public function getColorCssClass(): string;
}
