<?php

namespace LiquidSpace\Request;

use LiquidSpace\Entity\Workspace\AvailabilityGranularity;
use LiquidSpace\Response\WorkspaceAvailabilityResponse;

class WorkspaceAvailabilityRequest implements RequestInterface
{
    public function __construct(
        private readonly string $venueId,
        private readonly string $workspaceId,
        private readonly \DateTimeImmutable $date,
        private readonly AvailabilityGranularity $granularity = AvailabilityGranularity::FifteenMinutes,
    ) {
    }

    public static function getResponseClass(): string
    {
        return WorkspaceAvailabilityResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
    }

    public function getPath(): string
    {
        return sprintf(
            '/marketplace/api/venues/%s/workspaces/%s/availability/hourly/%s',
            $this->venueId,
            $this->workspaceId,
            $this->date->format('Y-m-d')
        );
    }

    public function getOptions(): array
    {
        return [
            'query' => [
                'timeChunkSize' => $this->granularity->value,
            ]
        ];
    }

    public function requiresEnterpriseToken(): bool
    {
        return false;
    }
}
