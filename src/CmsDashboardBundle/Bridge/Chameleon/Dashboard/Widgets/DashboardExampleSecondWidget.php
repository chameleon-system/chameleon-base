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

final class DashboardExampleSecondWidget extends DashboardWidget
{
    public function getTitle(): string
    {
        return 'Example 2 Module Title';
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
                <div class=\"p-3\">
                  <h4 class=\"card-title mb-0\">Traffic</h4>
                  <div class=\"card-text\">January - July 2024</div>
                </div>
<div class='bg-white'>
<canvas id=\"chart2\"></canvas>
</div>
        <script>
        const chartData2 = {
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
                'chart2',
                chartData2.labels,
                chartData2.datasets,
                chartData2.options,
                chartData2.additionalConfig
            );
        };
    </script>
    </div>
";
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
        return 'text-white bg-danger';
    }
}