<?php

namespace LiquidSpaceClient\Response;

use LiquidSpaceClient\Entity\Venue\Venue;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VenueResponse
{
    public readonly Venue $venue;

    public function __construct(ResponseInterface $response)
    {
        $venueData = $response->toArray();

        $this->venue = new Venue($venueData);
    }
}
