<?php

namespace LiquidSpaceClient\Entity\Venue;

use LiquidSpaceClient\Entity\Workspace\Workspace;

class Venue
{
    public readonly string $id;
    public readonly string $name;
    public readonly float $latitude;
    public readonly float $longitude;
    public readonly string $address;
    public readonly ?string $description;
    /** @var string[] */
    public readonly array $imageUrls;
    public readonly string $url;
    public readonly float $averageRating;
    public readonly int $ratingCount;
    public readonly bool $hasHealthAndSafetyPolicy;
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
        $this->description = $venueData['description'];
        $this->imageUrls = $venueData['imageUrls'];
        $this->url = $venueData['url'];
        $this->averageRating = $venueData['averageRating'];
        $this->ratingCount = $venueData['ratingCount'];
        $this->hasHealthAndSafetyPolicy = $venueData['hasHealthAndSafetyPolicy'];
        $this->hasLiquidSpacePro = $venueData['hasLiquidspacePro'];
        $this->mapImageUrl = $venueData['venueMapImageUrl'];

        $workspaces = [];
        foreach ($venueData['workspaces'] as $workspaceData) {
            $workspaces[] = new Workspace($workspaceData);
        }
        $this->workspaces = $workspaces;
    }
}
