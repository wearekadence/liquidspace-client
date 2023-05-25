<?php

namespace Client\Request;

use Client\Response\VenueListAutoCompleteResponse;

class VenueListAutoCompleteRequest implements RequestInterface
{
    public function __construct(
        private readonly string $searchString
    ) {
    }

    public function getMethod(): HttpMethod
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

    public function getResponseClass(): string
    {
        return VenueListAutoCompleteResponse::class;
    }
}