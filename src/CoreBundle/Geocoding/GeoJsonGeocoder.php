<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Geocoding;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoJsonGeocoder implements GeocoderInterface
{
    /** @var string */
    private $endpoint;

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        string $endpoint,
        HttpClientInterface $httpClient,
        LoggerInterface $logger
    ) {
        $this->endpoint = $endpoint;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * @return GeocodingResult[]
     *
     * @psalm-return list<GeocodingResult>
     */
    public function geocode(string $query): array
    {
        $url = str_replace('{query}', urlencode($query), $this->endpoint);
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Chameleon System / https://chameleon-system.com',
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            $this->logger->warning('Received non-200 response from geocoding endpoint {url}: {statusCode} {body}', ['url' => $url, 'statusCode' => $response->getStatusCode(), 'body' => $response->getContent()]);

            return [];
        }

        /**
         * @see https://geojson.org/
         *
         * @psalm-var array{
         *      type: "FeatureCollection",
         *      features: list<array{
         *          type: "Feature",
         *          geometry: array{
         *              type: "Point"
         *              coordinates: [ float, float ]
         *          },
         *          properties: array<string, mixed>
         *      }>
         * } $json
         */
        $json = json_decode($response->getContent(), true);

        /** @psalm-var list<GeocodingResult|null> $results */
        $results = array_map(
            function (array $item) {
                $name = $item['properties']['display_name'] ?? null;
                $longitude = $item['geometry']['coordinates'][0] ?? null;
                $latitude = $item['geometry']['coordinates'][1] ?? null;

                if (null === $latitude || null === $longitude) {
                    return null;
                }

                return new GeocodingResult($name, $latitude, $longitude);
            },
            $json['features'] ?? []
        );

        return array_values(array_filter($results));
    }
}
