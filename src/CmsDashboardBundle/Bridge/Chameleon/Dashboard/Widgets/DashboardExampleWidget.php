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
use ChameleonSystem\CoreBundle\Translation\ChameleonTranslator;
use esono\pkgCmsCache\CacheInterface;

final class DashboardExampleWidget extends DashboardWidget
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly ChameleonTranslator $translator)
    {
        parent::__construct($cache);
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
        return "<div>This is a test widget</div>";
    }

    public function getFooterHtml(): string
    {
        $cacheCreationTime = $this->getCacheCreationTime();
        if (null === $cacheCreationTime) {
            return '';
        }

        $formattedTime = date('Y-m-d H:i:s', $cacheCreationTime);

        return "<div class='px-3 py-2'>letzte Aktualisierung: ".$formattedTime."</div>";
    }

    public function getColorCssClass(): string
    {
        return 'text-white bg-info';
    }
}
