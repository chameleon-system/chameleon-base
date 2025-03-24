if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};

CHAMELEON.CORE.Charts = {
    generateChart: function (chartId, labels, datasets, options = {}, additionalConfig = {}) {
        const config = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets,
            },
            options: {
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: (context) => {
                                const datasetLabel = context.dataset.label || '';
                                const value = context.raw;
                                const formattedValue = CHAMELEON.CORE.Charts.formatValue(value, additionalConfig.hasCurrency);

                                if (additionalConfig.hasCurrency) {
                                    return `${datasetLabel}: ${formattedValue} `+ additionalConfig.currency.symbol;
                                } else {
                                    return `${datasetLabel}: ${formattedValue}`;
                                }
                            },
                        },
                    },
                    legend: options.legend || {},
                },
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                    },
                },
            },
            plugins: [
                {
                    id: 'stackedSum',
                    beforeDraw: (chart) => {

                        if (false == additionalConfig.displayGraphLabels) {
                            return;
                        }

                        const { ctx, chartArea, scales } = chart;
                        const datasets = chart.data.datasets;
                        const labels = chart.data.labels;

                        labels.forEach((label, index) => {
                            let sum = 0;

                            datasets.forEach((dataset) => {
                                if (undefined !== dataset.data[index]) {
                                    sum += dataset.data[index];
                                }
                            });

                            const roundedSum = CHAMELEON.CORE.Charts.formatValue(sum, additionalConfig.hasCurrency);

                            // Position and Rotation
                            ctx.save();
                            ctx.font = 'bold 12px Arial';
                            ctx.fillStyle = '#989FA5';
                            ctx.translate(
                                scales.x.getPixelForValue(label),
                                chartArea.top + 55
                            );
                            ctx.rotate(-Math.PI / 4); // rotate 45° to the left

                            if (additionalConfig.hasCurrency) {
                                ctx.fillText(roundedSum + additionalConfig.currency.symbol, -5, 0);
                            } else {
                                ctx.fillText(roundedSum, -5, 0);
                            }

                            ctx.restore();
                        });
                    },
                },
            ],
        };

        const chart = new Chart(document.getElementById(chartId), config);

        CHAMELEON.CORE.Charts.increaseYAxisHeight(chart);
    },

    increaseYAxisHeight: function (chart) {
        const maxHeight = chart.scales.y.end;
        const increasedMaxHeight = maxHeight + (maxHeight / 100 * 10);
        chart.config.options.scales.y.suggestedMax = increasedMaxHeight;
        chart.update();
    },

    formatValue(value, hasCurrency) {
        let roundedSum = 0;

        if (hasCurrency) {
            roundedSum = new Intl.NumberFormat('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(value);
        } else {

            if (value % 1 !== 0) {
                roundedSum = new Intl.NumberFormat('de-DE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }).format(value);
            } else {
                roundedSum = new Intl.NumberFormat('de-DE', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                }).format(value);
            }
        }

        return roundedSum;
    }
};
