<?php

namespace Client\Response;

use Client\Entity\Venue\AutocompleteVenue;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function PHPUnit\Framework\isEmpty;

class VenueListAutoCompleteResponse
{
    /** @var AutocompleteVenue[] */
    public readonly array $venues;

    public function __construct(ResponseInterface $response)
    {
        $content = $response->toArray();

        $venues = [];
        foreach ($content as $venue) {
            $venues[] = new AutocompleteVenue($venue);
        }
        $this->venues = $venues;
    }
}
