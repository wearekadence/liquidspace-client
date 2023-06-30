<?php

namespace LiquidSpace\Entity\Venue;

use LiquidSpace\Entity\Workspace\WorkspaceSearchResult;

class VenueSearchResult
{
    public readonly string $id;
    public readonly string $name;
    public readonly float $latitude;
    public readonly float $longitude;
    public readonly string $address;
    public readonly float $minPrice;
    public readonly string $minPriceFormatted;
    public readonly ?string $imageUrl;

    /** @var string[] */
    public readonly array $imageUrls;
    /** @var string[] */
    public readonly array $workspaceTypesFormatted;
    public readonly string $url;
    public readonly float $averageRating;
    public readonly int $ratingCount;
    public readonly bool $hasHealthAndSafetyPolicy;
    public readonly bool $hasLiquidSpacePro;
    public readonly bool $enterpriseHeadquarter;
    public readonly bool $enterprisePreferredLocation;
    public readonly bool $isExtendedNetwork;
    /** @var WorkspaceSearchResult[] */
    public readonly array $workspaces;
    public readonly int $defaultBookingMethod;
    public readonly int $defaultBookingMethodHourly;

    public function __construct(array $venueData)
    {
        $this->id = $venueData['venueId'];
        $this->name = $venueData['name'];
        $this->latitude = $venueData['latitude'];
        $this->longitude = $venueData['longitude'];
        $this->address = $venueData['address'];
        $this->minPrice = $venueData['minPrice'];
        $this->minPriceFormatted = $venueData['minPriceFormatted'];
        $this->imageUrl = $venueData['imageUrl'];
        $this->imageUrls = $venueData['imageUrls'];
        $this->workspaceTypesFormatted = $venueData['workspaceTypesFormatted'];
        $this->url = $venueData['url'];
        $this->averageRating = $venueData['averageRating'];
        $this->ratingCount = $venueData['ratingCount'];
        $this->hasHealthAndSafetyPolicy = $venueData['hasHealthAndSafetyPolicy'];
        $this->hasLiquidSpacePro = $venueData['hasLiquidspacePro'];
        $this->enterpriseHeadquarter = $venueData['enterpriseHeadquarter'];
        $this->enterprisePreferredLocation = $venueData['enterprisePreferredLocation'];
        $this->isExtendedNetwork = $venueData['isExtendedNetwork'];

        $workspaces = [];
        foreach ($venueData['workspaceSearchResults'] as $workspaceData) {
            $workspaces[] = new WorkspaceSearchResult($workspaceData);
        }
        $this->workspaces = $workspaces;
        $this->defaultBookingMethod = $venueData['defaultBookingMethod'];
        $this->defaultBookingMethodHourly = $venueData['defaultBookingMethodHourly'];
    }
}
