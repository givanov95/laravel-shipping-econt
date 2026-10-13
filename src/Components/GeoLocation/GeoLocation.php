<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt\Components\GeoLocation;

class GeoLocation
{
    public ?float $latitude;

    public ?float $longitude;

    public ?int $confidence;

    public function __construct(
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $confidence = null
    ) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->confidence = $confidence;
    }
}
