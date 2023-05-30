<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Venue\GeoLocation;
use LiquidSpace\Entity\Venue\VenueSearchResult;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VenueSearchResponse
{
    public readonly GeoLocation $geoLocation;
    /** @var VenueSearchResult[] */
    public readonly array $venues;

    public function __construct(ResponseInterface $response)
    {
        $searchData = $response->toArray();

        $this->geoLocation = new GeoLocation($searchData['geoLocation']);
        $this->venues = array_map(
            fn (array $venueData) => new VenueSearchResult($venueData),
            $searchData['venues'],
        );
    }
}
