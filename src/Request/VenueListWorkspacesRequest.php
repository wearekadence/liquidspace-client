<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\VenueListWorkspacesResponse;

class VenueListWorkspacesRequest implements RequestInterface
{
    public function __construct(
        private readonly string $venueId
    ) {
    }

    public static function getResponseClass(): string
    {
        return VenueListWorkspacesResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return sprintf('/marketplace/api/venues/%s/workspaces', $this->venueId);
    }

    public function getOptions(): array
    {
        return [];
    }

    public function requiresEnterpriseToken(): bool
    {
        return false;
    }

    public function canImpersonate(): bool
    {
        return true;
    }
}
