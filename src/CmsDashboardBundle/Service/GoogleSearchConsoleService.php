<?php

namespace ChameleonSystem\CmsDashboardBundle\Service;

use Google\Client;
use Google\Exception;
use Google\Service\SearchConsole;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Psr\Log\LoggerInterface;

class GoogleSearchConsoleService
{
    private Client $client;
    private SearchConsole $searchConsole;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $googleSearchConsoleAuthJson)
    {
        $this->initClient();
        $this->searchConsole = new SearchConsole($this->client);
    }

    /**
     * @throws Exception
     */
    private function initClient(): void
    {
        if ('' === $this->googleSearchConsoleAuthJson) {
            throw new \RuntimeException('Google Auth JSON is not set.');
        }

        $authConfig = json_decode($this->googleSearchConsoleAuthJson, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('Invalid Google Auth JSON format: '.json_last_error_msg());
        }

        $this->client = new Client();
        $this->client->setApplicationName('Chameleon Google Search Console Integration');
        $this->client->setAuthConfig($authConfig);
        $this->client->setScopes([SearchConsole::WEBMASTERS_READONLY]);
    }

    public function getSearchAnalytics(string $siteUrl, string $startDate, string $endDate, array $dimensions = ['date']): array
    {
        try {
            $this->client->fetchAccessTokenWithAssertion(); // Ensure authentication

            $request = new SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setDimensions($dimensions);

            $response = $this->searchConsole->searchanalytics->query($siteUrl, $request);

            return $response->getRows() ?: [];
        } catch (\Exception $e) {
            $this->logger->error('Google Search Console API Error: '.$e->getMessage());

            return [];
        }
    }

    public function getComparisonData(string $siteUrl, string $currentStart, string $currentEnd, string $previousStart, string $previousEnd): array
    {
        $currentData = $this->getSearchAnalytics($siteUrl, $currentStart, $currentEnd);
        $previousData = $this->getSearchAnalytics($siteUrl, $previousStart, $previousEnd);

        $currentDataSearchQueries = $this->getSearchAnalytics($siteUrl, $currentStart, $currentEnd, ['query']);
        $previousDataSearchQueries = $this->getSearchAnalytics($siteUrl, $previousStart, $previousEnd, ['query']);

        return [
            'current' => $this->formatChartData($currentData),
            'previous' => $this->formatChartData($previousData),
            'topImprovedQueries' => $this->getTopImprovedQueries($currentDataSearchQueries, $previousDataSearchQueries),
        ];
    }

    private function formatChartData(array $searchConsoleData): array
    {
        $labels = [];
        $clicks = [];
        $impressions = [];

        foreach ($searchConsoleData as $row) {
            $labels[] = $row->keys[0]; // Query
            $clicks[] = $row->clicks;
            $impressions[] = $row->impressions;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'clicks',
                    'data' => $clicks,
                ],
                [
                    'label' => 'impressions',
                    'data' => $impressions,
                ],
            ],
        ];
    }

    private function getTopImprovedQueries(array $currentData, array $previousData, int $maxResults = 10): array
    {
        $improvementData = [];

        $previousClicks = [];
        $previousImpressions = [];
        foreach ($previousData as $row) {
            $previousClicks[$row->keys[0]] = $row->clicks ?? 0;
            $previousImpressions[$row->keys[0]] = $row->impressions ?? 0;
        }

        foreach ($currentData as $row) {
            $query = $row->keys[0];
            $currentClicks = $row->clicks ?? 0;
            $previousClicksCount = $previousClicks[$query] ?? 0;
            $previousImpressionsCount = $previousImpressions[$query] ?? 0;
            $clickDifference = $currentClicks - $previousClicksCount;

            $improvementData[] = [
                'query' => $query,
                'currentClicks' => $currentClicks,
                'previousClicks' => $previousClicksCount,
                'difference' => $clickDifference,
                'currentImpressions' => $row->impressions ?? 0,
                'previousImpressions' => $previousImpressionsCount,
            ];
        }

        usort($improvementData, fn ($a, $b) => $b['difference'] <=> $a['difference']);

        return array_slice($improvementData, 0, $maxResults);
    }
}
