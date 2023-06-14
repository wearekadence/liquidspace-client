<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\VenueListAutoCompleteResponse;

class VenueListAutoCompleteRequest implements RequestInterface
{
    public function __construct(
        private readonly string $searchString
    ) {
    }

    public static function getResponseClass(): string
    {
        return VenueListAutoCompleteResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return '/marketplace/api/venues/search';
    }

    public function getOptions(): array
    {
        return [
            'query' => [
                'term' => $this->searchString,
            ],
        ];
    }

    public function requiresEnterpriseToken(): bool
    {
        return false;
    }
}
