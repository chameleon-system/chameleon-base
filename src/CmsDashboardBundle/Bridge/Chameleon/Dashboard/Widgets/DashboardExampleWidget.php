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

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DashboardExampleWidget extends DashboardWidget
{
    public function __construct(
        private readonly DashboardCacheService $dashboardCacheService,
        private readonly TranslatorInterface $translator
    ) {
        parent::__construct($dashboardCacheService, $translator);
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Orders without shipping');
    }

    public function getDropdownItems(): array
    {
        $button1 = new WidgetDropdownItemDataModel('example', 'Button 1', 'https://example.com');
        $button2 = new WidgetDropdownItemDataModel('example2', 'Button 2', 'https://example.com');

        return [$button1, $button2];
    }

    protected function generateBodyHtml(): string
    {
        return '<div>This is a test widget</div>';
    }

    public function getColorCssClass(): string
    {
        return 'text-white bg-info';
    }

    public function getChartId(): string
    {
        return 'example';
    }
}
