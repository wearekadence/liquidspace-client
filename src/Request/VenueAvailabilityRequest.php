<?php

namespace LiquidSpace\Request;

use LiquidSpace\Entity\Workspace\AvailabilityGranularity;
use LiquidSpace\Response\VenueAvailabilityResponse;

class VenueAvailabilityRequest implements RequestInterface
{
    public function __construct(
        private readonly string $venueId,
        private readonly \DateTimeImmutable $date,
        private readonly AvailabilityGranularity $granularity = AvailabilityGranularity::FifteenMinutes,
    ) {
    }

    public static function getResponseClass(): string
    {
        return VenueAvailabilityResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return sprintf(
            '/marketplace/api/venues/%s/availability/hourly/%s',
            $this->venueId,
            $this->date->format('Y-m-d')
        );
    }

    public function getOptions(): array
    {
        return [
            'query' => [
                'timeChunkSize' => $this->granularity->value,
            ],
        ];
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
