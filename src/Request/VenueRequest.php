<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\VenueResponse;

class VenueRequest implements RequestInterface
{
    public function __construct(
        private readonly string $venueId
    ) {
    }

    public static function getResponseClass(): string
    {
        return VenueResponse::class;
    }

    public static function getMethod(): HttpMethod
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
}
