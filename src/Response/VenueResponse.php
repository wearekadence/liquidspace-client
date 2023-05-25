<?php

namespace Client\Response;

use Client\Entity\Venue\Venue;
use Client\Entity\Workspace\Workspace;
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