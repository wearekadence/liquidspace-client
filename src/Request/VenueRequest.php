<?php

namespace Client\Request;

use Client\Response\VenueResponse;

class VenueRequest implements RequestInterface
{
    public function __construct(
        private readonly string $venueId
    ) {
    }

    public function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return sprintf('/marketplace/api/venues/%s', $this->venueId);
    }

    public function getOptions(): array
    {
        return [];
    }

    public function getResponseClass(): string
    {
        return VenueResponse::class;
    }
}