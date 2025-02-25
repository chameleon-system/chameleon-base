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
use ChameleonSystem\CmsDashboardBundle\Library\Constants\GoogleMetric;
use ChameleonSystem\CmsDashboardBundle\Library\Constants\GoogleDimension;

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

        try {
            $options = [
                'credentials' => json_decode($this->googleAnalyticsAuthJson, true, 512, JSON_THROW_ON_ERROR),
            ];
        } catch (\JsonException $e) {
            $this->logger->error(
                'Unable to decode Google Analytics auth json: {errorMessage}',
                ['errorMessage' => $e->getMessage(), 'json' => $this->googleAnalyticsAuthJson]
            );

            return;
        }

        $this->client = new BetaAnalyticsDataClient($options);
    }

    public function getEngagementRate(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array {
        $engagementCurrent = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            [GoogleMetric::ENGAGEMENT_RATE],
            [GoogleDimension::DATE],
            [$this->getDateOrderBy()]
        );
        $engagementPrevious = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            [GoogleMetric::ENGAGEMENT_RATE],
            [GoogleDimension::DATE],
            [$this->getDateOrderBy()]
        );

        $totalEngagementCurrent = array_reduce(
            $engagementCurrent,
            fn($carry, $item) => $carry + ($item['metric_0'] ?? 0),
            0
        );
        $totalEngagementPrevious = array_reduce(
            $totalEngagementPrevious,
            fn($carry, $item) => $carry + ($item['metric_0'] ?? 0),
            0
        );

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
        $sessionDurationCurrent = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            [GoogleMetric::AVERAGE_SESSION_DURATION],
            [GoogleDimension::DATE],
            [$sessionOrderBy]
        );

        $sessionDurationPrevious = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            [GoogleMetric::AVERAGE_SESSION_DURATION],
            [GoogleDimension::DATE],
            [$this->getDateOrderBy()]
        );

        $avgSessionCurrentCount = count($sessionDurationCurrent);
        $avgSessionPreviousCount = count($sessionDurationPrevious);

        $avgSessionCurrent = $avgSessionCurrentCount > 0 ? array_sum(array_column($sessionDurationCurrent, 'metric_0'))
            / $avgSessionCurrentCount : 0;
        $avgSessionPrevious = $avgSessionPreviousCount > 0 ? array_sum(
                array_column($sessionDurationPrevious, 'metric_0')
            ) / $avgSessionPreviousCount : 0;

        return [
            'current' => $sessionDurationCurrent,
            'previous' => $sessionDurationPrevious,
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
            [GoogleMetric::ENGAGED_SESSIONS, GoogleMetric::CONVERSIONS],
            [GoogleDimension::COUNTRY, GoogleDimension::REGION]
        );

        return $geoLocation;
    }

    public function getTrafficSource(
        string $propertyId,
        string $currentStart,
        string $currentEnd,
        string $previousStart,
        string $previousEnd
    ): array {
        $currentTrafficSource = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            [GoogleMetric::ENGAGED_SESSIONS],
            [GoogleDimension::SESSION_DEFAULT_CHANNEL_GROUP]
        );

        $previousTrafficSource = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            [GoogleMetric::ENGAGED_SESSIONS],
            [GoogleDimension::SESSION_DEFAULT_CHANNEL_GROUP]
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
    ): array {
        $currentUtmTracking = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            [GoogleMetric::ENGAGED_SESSIONS, GoogleMetric::CONVERSIONS],
            [GoogleDimension::CAMPAIGN_NAME, GoogleDimension::SOURCE, GoogleDimension::MEDIUM]
        );

        $previousUtmTracking = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            [GoogleMetric::ENGAGED_SESSIONS, GoogleMetric::CONVERSIONS],
            [GoogleDimension::CAMPAIGN_NAME, GoogleDimension::SOURCE, GoogleDimension::MEDIUM]
        );

        return $this->processUtmTrackingData($currentUtmTracking, $previousUtmTracking);
    }

    private function processUtmTrackingData(array $currentData, array $previousData): array
    {
        $utmTracking = [];

        $previousLookup = [];
        foreach ($previousData as $entry) {
            $key = $entry['dimension_0'].'|'.$entry['dimension_1'].'|'.$entry['dimension_2'];
            $previousLookup[$key] = [
                GoogleMetric::SESSIONS => $entry['metric_0'],
                GoogleMetric::CONVERSIONS => $entry['metric_1'],
            ];
        }

        foreach ($currentData as $entry) {
            $key = $entry['dimension_0'].'|'.$entry['dimension_1'].'|'.$entry['dimension_2'];
            $utmTracking[] = [
                'campaign' => $entry['dimension_0'],
                'source' => $entry['dimension_1'],
                'medium' => $entry['dimension_2'],
                'current_sessions' => $entry['metric_0'],
                'current_conversions' => $entry['metric_1'],
                'previous_sessions' => $previousLookup[$key]['sessions'] ?? 0,
                'previous_conversions' => $previousLookup[$key][GoogleMetric::CONVERSIONS] ?? 0,
                'session_change' => isset($previousLookup[$key]) ?
                    ($entry['metric_0'] - ($previousLookup[$key]['sessions'] ?? 0)) : null,
                'conversion_change' => isset($previousLookup[$key]) ?
                    ($entry['metric_1'] - ($previousLookup[$key][GoogleMetric::CONVERSIONS] ?? 0)) : null,
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
    ): array {
        $current = $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            [GoogleMetric::ITEM_VIEWS, GoogleMetric::ADD_TO_CARTS, GoogleMetric::ECOMMERCE_PURCHASES]
        );

        $previous = $this->fetchAnalyticsData(
            $propertyId,
            $previousStart,
            $previousEnd,
            [GoogleMetric::ITEM_VIEWS, GoogleMetric::ADD_TO_CARTS, GoogleMetric::ECOMMERCE_PURCHASES]
        );

        return $this->formatECommerceChartData($current, $previous);
    }

    private function formatECommerceChartData(array $currentData, $previousData): array
    {
        return [
            'current' => [
                array_sum(array_column($currentData, 'metric_0')),
                array_sum(array_column($currentData, 'metric_1')),
                array_sum(array_column($currentData, 'metric_2')),
            ],
            'previous' => [
                array_sum(array_column($previousData, 'metric_0')),
                array_sum(array_column($previousData, 'metric_1')),
                array_sum(array_column($previousData, 'metric_2')),
            ],
        ];
    }

    public function getDemographyData(
        string $propertyId,
        string $currentStart,
        string $currentEnd
    ): array {
        return $this->fetchAnalyticsData(
            $propertyId,
            $currentStart,
            $currentEnd,
            [GoogleMetric::ENGAGED_SESSIONS],
            [GoogleDimension::USER_AGE_BRACKET, GoogleDimension::USER_GENDER]
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
                [GoogleMetric::SESSIONS, GoogleMetric::CONVERSIONS],
                [GoogleDimension::DEVICE_CATEGORY]
            ),
            'previous' => $this->fetchAnalyticsData(
                $propertyId,
                $previousStart,
                $previousEnd,
                [GoogleMetric::SESSIONS, GoogleMetric::CONVERSIONS],
                [GoogleDimension::DEVICE_CATEGORY]
            ),
        ];
    }

    private function fetchAnalyticsData(
        string $propertyId,
        string $startDate,
        string $endDate,
        array $metrics,
        array $dimensions = [GoogleDimension::DATE],
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
            'order_bys' => $orderBys,
        ]);

        try {
            $response = $this->client->runReport($request);

            return $this->formatChartData($response);
        } catch (\Exception $e) {
            $this->logger->error('Google Analytics API Error: '.$e->getMessage());

            return [];
        }
    }

    private function formatChartData(RunReportResponse $response): array
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

    private function getDateOrderBy(): OrderBy
    {
        return (new OrderBy())->setDimension(
            (new DimensionOrderBy())
                ->setDimensionName(GoogleDimension::DATE)
                ->setOrderType(DimensionOrderBy::NUMERIC)
        );
    }
}
