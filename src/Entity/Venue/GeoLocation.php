<?php

namespace LiquidSpace\Entity\Venue;

class GeoLocation
{
    public readonly float $latitude;
    public readonly float $longitude;

    public function __construct(array $geoLocationData)
    {
        $this->latitude = $geoLocationData['latitude'];
        $this->longitude = $geoLocationData['longitude'];
    }
}
