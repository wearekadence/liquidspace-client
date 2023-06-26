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
        $this->id = $venueData['id'];
        $this->name = $venueData['name'];
        $this->latitude = $venueData['latitude'];
        $this->longitude = $venueData['longitude'];
        $this->address = $venueData['address'];
        $this->addressLine1 = $venueData['simpleAddress1'];
        $this->addressLine2 = $venueData['simpleAddress2'];
        $this->city = $venueData['city'];
        $this->county = $venueData['state'];
        $this->postalCode = $venueData['zip'];
        // Convert UK to official ISO 3166-1 alpha-2 code
        $this->countryCode = match($venueData['country']) {
            'UK' => 'GB',
            default => $venueData['country'],
        };
        $this->timeZoneId = $venueData['timeZone']['tzdbIds'][0] ?? 'UTC';
        $this->description = $venueData['description'];
        $this->imageUrls = $venueData['imageUrls'];
        $this->url = $venueData['url'];
        $this->averageRating = $venueData['averageRating'];
        $this->ratingCount = $venueData['ratingCount'];
        $this->healthAndSafetyPolicy = $venueData['healthAndSafetyPolicy'];
        $this->hasLiquidSpacePro = $venueData['hasLiquidspacePro'];
        $this->mapImageUrl = $venueData['venueMapImageUrl'];

        $workspaces = [];
        foreach ($venueData['hourlyWorkspaces'] as $workspaceData) {
            $workspaces[] = new Workspace($workspaceData);
        }
        $this->workspaces = $workspaces;
    }
}
