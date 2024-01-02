<?php

namespace LiquidSpace\Entity\Venue;

use LiquidSpace\Entity\Workspace\Workspace;

class Venue
{
    public readonly string $id;
    public readonly string $name;
    public readonly float $latitude;
    public readonly float $longitude;
    public readonly string $address;
    public readonly string $addressLine1;
    public readonly ?string $addressLine2;
    public readonly string $city;
    public readonly ?string $county;
    public readonly ?string $postalCode;
    public readonly string $countryCode;
    public readonly string $timeZoneId;
    public readonly ?string $description;
    /** @var string[] */
    public readonly array $imageUrls;
    public readonly string $url;
    public readonly float $averageRating;
    public readonly int $ratingCount;
    public readonly ?string $healthAndSafetyPolicy;
    public readonly bool $hasLiquidSpacePro;
    public readonly string $mapImageUrl;
    /** @var Workspace[] */
    public readonly array $workspaces;

    public function __construct(array $venueData)
    {
        if (!isset($venueData['id'])) {
            throw new \InvalidArgumentException('Venue data must contain an id');
        }
        $this->id = $venueData['id'];

        if (!isset($venueData['name'])) {
            throw new \InvalidArgumentException('Venue data must contain a name');
        }
        $this->name = $venueData['name'];

        if (!isset($venueData['latitude'])) {
            throw new \InvalidArgumentException('Venue data must contain a latitude');
        }
        $this->latitude = $venueData['latitude'];

        if (!isset($venueData['longitude'])) {
            throw new \InvalidArgumentException('Venue data must contain a longitude');
        }
        $this->longitude = $venueData['longitude'];

        if (!isset($venueData['address'])) {
            throw new \InvalidArgumentException('Venue data must contain an address');
        }
        $this->address = $venueData['address'];

        if (!isset($venueData['simpleAddress1'])) {
            throw new \InvalidArgumentException('Venue data must contain an address line 1');
        }
        $this->addressLine1 = $venueData['simpleAddress1'];
        $this->addressLine2 = $venueData['simpleAddress2'];

        if (!isset($venueData['city'])) {
            throw new \InvalidArgumentException('Venue data must contain a city');
        }
        $this->city = $venueData['city'];
        $this->county = $venueData['state'];
        $this->postalCode = $venueData['zip'];
        // Convert UK to official ISO 3166-1 alpha-2 code
        if (!isset($venueData['country'])) {
            throw new \InvalidArgumentException('Venue data must contain a country code');
        }
        $this->countryCode = match ($venueData['country']) {
            'UK' => 'GB',
            default => $venueData['country'],
        };
        $this->timeZoneId = $venueData['timeZone']['tzdbIds'][0] ?? 'UTC';
        $this->description = $venueData['description'];
        $this->imageUrls = $venueData['imageUrls'] ?? [];

        if (!isset($venueData['url'])) {
            throw new \InvalidArgumentException('Venue data must contain a url');
        }
        $this->url = $venueData['url'];

        if (!isset($venueData['averageRating'])) {
            throw new \InvalidArgumentException('Venue data must contain an average rating');
        }
        $this->averageRating = $venueData['averageRating'];
        $this->ratingCount = $venueData['ratingCount'] ?? 0;
        $this->healthAndSafetyPolicy = $venueData['healthAndSafetyPolicy'];
        $this->hasLiquidSpacePro = $venueData['hasLiquidspacePro'] ?? false;

        if (!isset($venueData['venueMapImageUrl'])) {
            throw new \InvalidArgumentException('Venue data must contain a map image url');
        }
        $this->mapImageUrl = $venueData['venueMapImageUrl'];

        $workspaces = [];
        if (isset($venueData['hourlyWorkspaces']) && \is_array($venueData['hourlyWorkspaces'])) {
            foreach ($venueData['hourlyWorkspaces'] as $workspaceData) {
                $workspaces[] = new Workspace($workspaceData);
            }
        }
        $this->workspaces = $workspaces;
    }
}
