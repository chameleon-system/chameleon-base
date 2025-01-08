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
        return $this->translator->trans('Umsatz ohne Versand');
    }

    public function getDropdownItems(): array
    {
        $button1 = new WidgetDropdownItemDataModel('example', 'Button 1', 'https://example.com');
        $button2 = new WidgetDropdownItemDataModel('example2', 'Button 2', 'https://example.com');

        return [$button1, $button2];
    }

    protected function generateBodyHtml(): string
    {
        return "<div>
<div class='bg-white'>
<canvas id=\"chart1\"></canvas>
</div>
        <script>
        const chartData1 = {
            labels: [
                                                                                '2024-KW1',
                                '2024-KW2',
                                '2024-KW3',
                                '2024-KW4',
                                '2024-KW5'
                                                                                                                                                                                                            ],
            datasets: [
                
                                                        {
                        label: 'Amazon',
                        data: [
                                                        1099.7,
                                                        109,
                                                        1785.39,
                                                        1124.7,
                                                        125
                                                    ],
                        backgroundColor: '#20a8d8',
                    }
                    ,                                        {
                        label: 'PayPal Express',
                        data: [
                                                        2822.39,
                                                        1399.75,
                                                        1121.84,
                                                        2835.34,
                                                        2828.19
                                                    ],
                        backgroundColor: '#6610f2',
                    }
                    ,                                        {
                        label: 'PayPal Checkout',
                        data: [
                                                        2712.65,
                                                        3345.18,
                                                        2169.92,
                                                        3486.9,
                                                        2168
                                                    ],
                        backgroundColor: '#6f42c1',
                    }
                    ,                                        {
                        label: 'Vorkasse',
                        data: [
                                                        602.9,
                                                        500,
                                                        30
                                                    ],
                        backgroundColor: '#e83e8c',
                    }
                    ,                                        {
                        label: 'PayPal',
                        data: [
                                                        1388.68,
                                                        766.8,
                                                        2427.9,
                                                        1495.4,
                                                        1619.89
                                                    ],
                        backgroundColor: '#f86c6b',
                    }
                                                                    ],
            options: {
                legend: {
                    display: true
                }
            },
            additionalConfig: {
                hasCurrency: true,
                                currency: {
                    symbol: '€'
                }
                            },
        };

        window.onload = function () {
            CHAMELEON.CORE.Charts.generateChart(
                'chart1',
                chartData1.labels,
                chartData1.datasets,
                chartData1.options,
                chartData1.additionalConfig
            );
        };
    </script>
    </div>
";
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

    public function getFooterIncludes(): array
    {
        $includes = parent::getFooterIncludes();
        $includes[] = '<script type="text/javascript" src="/bundles/chameleonsystemecommercestats/ecommerce_stats/js/chart.4.4.7.js"></script>';
        $includes[] = '<script type="text/javascript" src="/bundles/chameleonsystemecommercestats/ecommerce_stats/js/chart-init.4.4.7.js"></script>';

        return $includes;
    }

    public function getColorCssClass(): string
    {
        return 'text-white bg-info';
    }
}
