<?php

namespace ChameleonSystem\CmsDashboardBundle\Service;

use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1alpha\OrderBy\DimensionOrderBy\OrderType;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Client;
use Google\Service\SearchConsole;
use Psr\Log\LoggerInterface;

class GoogleAnalyticsDashboardService
{
    private ?BetaAnalyticsDataClient $client = null;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $googleAnalyticsAuthJson
    ) {
        $this->initClient();
    }

    private function initClient(): void
    {
        if ('' === $this->googleAnalyticsAuthJson) {
            return;
        }

        $authConfig = json_decode($this->googleAnalyticsAuthJson, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('Invalid Google Auth JSON format: '.json_last_error_msg());
        }

        $options = [
            'credentials' => json_decode($this->googleAnalyticsAuthJson, true),
        ];

        $this->client = new BetaAnalyticsDataClient($options);
    }

    public function getEngagementRate(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array {
        $engagementOrderBy = new OrderBy([
            'dimension' => new DimensionOrderBy([
                'dimension_name' => 'date',
                'order_type' => DimensionOrderBy\OrderType::NUMERIC,
            ])
        ]);

        $engagementCurrent = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            ['engagementRate'],
            ['date'],
            [$engagementOrderBy]
        );
        $engagementPrevious = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            ['engagementRate'],
            ['date'],
            [$engagementOrderBy]
        );

        $totalEngagementCurrent = array_sum(array_column($engagementCurrent, 'metric_0'));
        $totalEngagementPrevious = array_sum(array_column($engagementPrevious, 'metric_0'));

        return [
            'current' => $engagementCurrent,
            'previous' => $engagementPrevious,
            'totalEngagementCurrent' => $totalEngagementCurrent,
            'totalEngagementPrevious' => $totalEngagementPrevious,
        ];
    }

    public function getSessionDuration(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array {
        $sessionOrderBy = new OrderBy([
            'dimension' => new DimensionOrderBy([
                'dimension_name' => 'date',
                'order_type' => DimensionOrderBy\OrderType::NUMERIC,
            ])
        ]);

        $sessionDurationCurrent = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            ['averageSessionDuration'],
            ['date'],
            [$sessionOrderBy]
        );

        $sessionDurationPrevious = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            ['averageSessionDuration'],
            ['date'],
            [$sessionOrderBy]
        );

        $avgSessionCurrent = count($sessionDurationCurrent) > 0 ? array_sum(array_column($sessionDurationCurrent, 'metric_0')) / count($sessionDurationCurrent) : 0;
        $avgSessionPrevious = count($sessionDurationPrevious) > 0 ? array_sum(array_column($sessionDurationPrevious, 'metric_0')) / count($sessionDurationPrevious) : 0;

        return [
            'current' => $sessionDurationCurrent,
            'previous' => $sessionDurationCurrent,
            'avgSessionCurrent' => $avgSessionCurrent,
            'avgSessionPrevious' => $avgSessionPrevious,
        ];
    }

    public function getGeoLocation(
        string $propertyId,
        string $currentStart,
        string $currentEnd
    ): array {
        $geoLocation = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            ['engagedSessions', 'conversions'],
            ['country', 'region']
        );

        return $geoLocation;
    }

    public function getTrafficSource(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array{
        $currentTrafficSource = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            ['engagedSessions'],
            ['sessionDefaultChannelGroup']
        );

        $previousTrafficSource = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            ['engagedSessions'],
            ['sessionDefaultChannelGroup']
        );

        return [
            'current' => $currentTrafficSource,
            'previous' => $previousTrafficSource,
        ];
    }

    public function getUtmTracking(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array{
        $currentUtmTracking = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            ['engagedSessions', 'conversions'],
            ['campaignName', 'source', 'medium']
        );

        $previousUtmTracking = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            ['engagedSessions', 'conversions'],
            ['campaignName', 'source', 'medium']
        );

        return $this->processUtmTrackingData($currentUtmTracking, $previousUtmTracking);
    }

    private function processUtmTrackingData(array $currentData, array $previousData): array {
        $utmTracking = [];

        // Convert previous data into an associative array for easy lookup
        $previousLookup = [];
        foreach ($previousData as $entry) {
            $key = $entry['dimension_0'] . '|' . $entry['dimension_1'] . '|' . $entry['dimension_2'];
            $previousLookup[$key] = [
                'sessions' => $entry['metric_0'],
                'conversions' => $entry['metric_1']
            ];
        }

        // Process current data and match with previous
        foreach ($currentData as $entry) {
            $key = $entry['dimension_0'] . '|' . $entry['dimension_1'] . '|' . $entry['dimension_2'];
            $utmTracking[] = [
                'campaign' => $entry['dimension_0'],
                'source' => $entry['dimension_1'],
                'medium' => $entry['dimension_2'],
                'current_sessions' => $entry['metric_0'],
                'current_conversions' => $entry['metric_1'],
                'previous_sessions' => $previousLookup[$key]['sessions'] ?? 0, // Default to 0 if not found
                'previous_conversions' => $previousLookup[$key]['conversions'] ?? 0,
                'session_change' => isset($previousLookup[$key]) ?
                    ($entry['metric_0'] - $previousLookup[$key]['sessions']) : null,
                'conversion_change' => isset($previousLookup[$key]) ?
                    ($entry['metric_1'] - $previousLookup[$key]['conversions']) : null
            ];
        }

        return $utmTracking;
    }
    
    public function getECommerceChartData(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array{
        $current = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            ['itemViews', 'addToCarts', 'ecommercePurchases']
        );

        $previous = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            ['itemViews', 'addToCarts', 'ecommercePurchases']
        );

        return $this->formatECommerceChartData($current, $previous);
    }

    private function formatECommerceChartData(array $currentData, $previousData): array{
        return [
            'current' => [
                array_sum(array_column($currentData, 'metric_0')),
                array_sum(array_column($currentData, 'metric_1')),
                array_sum(array_column($currentData, 'metric_2'))
            ],
            'previous' => [
                array_sum(array_column($previousData, 'metric_0')),
                array_sum(array_column($previousData, 'metric_1')),
                array_sum(array_column($previousData, 'metric_2'))
            ],
        ];
    }

    public function getDemographyData(
        string $propertyId,
        string $currentStart,
        string $currentEnd
    ): array{
        return $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            ['engagedSessions'],
            ['userAgeBracket', 'userGender']
        );
    }

    public function getDeviceRatio(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array {
        return [
            'current' => $this->fetchAnalyticsData(
                $propertyId,
                $currentStart,
                $currentEnd,
                ['sessions', 'conversions'],
                ['deviceCategory']
            ),
            'previous' => $this->fetchAnalyticsData(
                $propertyId,
                $previousStart,
                $previousEnd,
                ['sessions', 'conversions'],
                ['deviceCategory']
            ),
        ];
    }

    private function fetchAnalyticsData(
        string $propertyId,
        string $startDate,
        string $endDate,
        array $metrics,
        array $dimensions = ['date'],
        array $orderBys = []
    ): array {
        if (null === $this->client) {
            return [];
        }

        $request = new RunReportRequest([
            'property' => 'properties/'.$propertyId,
            'date_ranges' => [new DateRange(['start_date' => $startDate, 'end_date' => $endDate])],
            'metrics' => array_map(fn($metric) => new Metric(['name' => $metric]), $metrics),
            'dimensions' => array_map(fn($dimension) => new Dimension(['name' => $dimension]), $dimensions),
            'order_bys' => $orderBys
        ]);

        try {
            $response = $this->client->runReport($request);

            return $this->formatChartData($response);
        } catch (\Exception $e) {
            $this->logger->error('Google Analytics API Error: '.$e->getMessage());

            return [];
        }
    }

    private function formatChartData($response): array
    {
        $formattedData = [];
        foreach ($response->getRows() as $row) {
            $dataPoint = [];
            foreach ($row->getDimensionValues() as $index => $dimensionValue) {
                $dataPoint['dimension_'.$index] = $dimensionValue->getValue();
            }
            foreach ($row->getMetricValues() as $index => $metricValue) {
                $dataPoint['metric_'.$index] = $metricValue->getValue();
            }
            $formattedData[] = $dataPoint;
        }

        return $formattedData;
    }
}
