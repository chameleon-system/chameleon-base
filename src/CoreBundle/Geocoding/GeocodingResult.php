<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Geocoding;

/**
 * @psalm-immutable
 */
class GeocodingResult
{
    /** @var string|null */
    private $name;

    /** @var float */
    private $latitude;

    /** @var float */
    private $longitude;

    public function __construct(?string $name, float $latitude, float $longitude)
    {
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
