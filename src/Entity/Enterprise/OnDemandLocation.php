<?php

namespace LiquidSpace\Entity\Enterprise;

class OnDemandLocation
{
    public readonly LocationType $locationType;
    public readonly string $id;
    public readonly string $description;
    public readonly string $name;
    public readonly ?string $city;
    public readonly ?string $state;
    public readonly ?float $latitude;
    public readonly ?float $longitude;
    public readonly bool $isGeofenceEnabled;
    public readonly ?float $geofenceRadiusMiles;

    public function __construct(array $onDemandLocationData)
    {
        $locationType = LocationType::tryFrom($onDemandLocationData['locationType']);
        if (null === $locationType) {
            throw new \InvalidArgumentException('Invalid location type: '.$onDemandLocationData['locationType']);
        }

        $this->locationType = $locationType;
        $this->id = $onDemandLocationData['id'];
        $this->description = $onDemandLocationData['description'];
        $this->name = $onDemandLocationData['name'];
        $this->city = $onDemandLocationData['city'];
        $this->state = $onDemandLocationData['state'];
        $this->latitude = $onDemandLocationData['latitude'];
        $this->longitude = $onDemandLocationData['longitude'];
        $this->isGeofenceEnabled = $onDemandLocationData['isGeofenceEnabled'];
        $this->geofenceRadiusMiles = $onDemandLocationData['geofenceRadiusMiles'];
    }
}
