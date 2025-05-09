document.addEventListener("widget:loaded", function (event) {
    const { serviceAlias, widgetElement } = event.detail;

    if (serviceAlias !== 'widget-search-console') return;

    const jsonDataEl = widgetElement.querySelector('#search-console-chart-data');
    const metaEl = widgetElement.querySelector('#search-console-chart-meta');

    console.log('LOL')
    if (!jsonDataEl || !metaEl) return;
    console.log('LOL')

    const { current, previous } = JSON.parse(jsonDataEl.textContent);
    const { labels, colors } = JSON.parse(metaEl.textContent);

    if (!current.labels.length || current.datasets.every(ds => ds.data.length === 0)) {
        console.warn("No data for Search Console chart.");
        return;
    }

    const ctx = widgetElement.querySelector('#searchConsoleChart')?.getContext('2d');
    console.log('LOL')
    if (!ctx) return;
    console.log('LOL')

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: current.labels,
            datasets: [
                {
                    ...current.datasets[0],
                    label: labels.clicks_current,
                    borderDash: [],
                    borderColor: colors.clicks_current_border,
                    backgroundColor: colors.clicks_current_bg,
                    yAxisID: 'yright',
                },
                {
                    ...current.datasets[1],
                    label: labels.impressions_current,
                    borderDash: [],
                    borderColor: colors.impressions_current_border,
                    backgroundColor: colors.impressions_current_bg,
                    yAxisID: 'yleft',
                },
                {
                    ...previous.datasets[0],
                    label: labels.clicks_previous,
                    borderDash: [5, 5],
                    borderColor: colors.clicks_previous_border,
                    backgroundColor: colors.clicks_previous_bg,
                    yAxisID: 'yright',
                },
                {
                    ...previous.datasets[1],
                    label: labels.impressions_previous,
                    borderDash: [5, 5],
                    borderColor: colors.impressions_previous_border,
                    backgroundColor: colors.impressions_previous_bg,
                    yAxisID: 'yleft',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day',
                        displayFormats: { day: 'dd.MM' }
                    },
                    title: {
                        display: true,
                        text: labels.xlabel
                    },
                    ticks: {
                        autoSkip: true,
                        maxRotation: 45,
                        minRotation: 45,
                        callback: function (value) {
                            return new Date(value).toLocaleDateString('de-DE', {
                                day: '2-digit',
                                month: '2-digit',
                                year: '2-digit'
                            });
                        }
                    }
                },
                yleft: {
                    position: 'left',
                    beginAtZero: true,
                    title: { display: true, text: labels.ylabel_impressions }
                },
                yright: {
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: labels.ylabel_clicks }
                }
            },
            plugins: {
                legend: { display: true, position: 'top' }
            }
        }
    });
});
