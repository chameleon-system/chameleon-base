<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Geocoding;

/**
 * A geocoder is responsible for finding coordinates from a given search query.
 * If no results exist, an empty array should be returned.
 */
interface GeocoderInterface
{
    /**
     * @return GeocodingResult[]
     *
     * @psalm-return list<GeocodingResult>
     */
    public function geocode(string $query): array;
}
