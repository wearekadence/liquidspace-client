<?php

namespace LiquidSpace\Request;

use LiquidSpace\Entity\Venue\ReservationMethod;
use LiquidSpace\Entity\Workspace\SpaceType;
use LiquidSpace\Response\SearchResponse;

class GeoSearchRequest implements RequestInterface
{
    private readonly ?int $reservationLengthMinutes;

    /**
     * @param SpaceType[] $spaceTypes
     */
    public function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly float $radius,
        private readonly \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime = null,
        int $reservationLengthMinutes = null,
        private readonly ?array $spaceTypes = null,
        private readonly ?int $minCapacity = null,
        private readonly ?array $amenityIds = null,
        private readonly ?float $minPrice = null,
        private readonly ?float $maxPrice = null,
        private readonly ?ReservationMethod $reservationMethod = null,
    ) {
        if (null === $reservationLengthMinutes && null !== $startTime && null !== $endTime) {
            $reservationLengthMinutes = (int) ceil(($endTime->getTimestamp() - $startTime->getTimestamp()) / 60);
        }
        $this->reservationLengthMinutes = $reservationLengthMinutes;
    }

    public static function getResponseClass(): string
    {
        return SearchResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Post;
    }

    public function getPath(): string
    {
        return '/marketplace/api/search/execute';
    }

    public function getOptions(): array
    {
        $providedOptions = [
            'discriminator' => 'LocationSearchRequest',
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'startTime' => $this->startTime->format(\DateTimeInterface::RFC3339),
        ];

        if (null !== $this->spaceTypes) {
            $providedOptions['spaceTypes'] = SpaceType::encode($this->spaceTypes);
        }

        if (null !== $this->minPrice) {
            $providedOptions['minPrice'] = $this->minPrice;
        }

        if (null !== $this->maxPrice) {
            $providedOptions['maxPrice'] = $this->maxPrice;
        }

        if (null !== $this->startTime) {
            $providedOptions['startTime'] = $this->startTime->format(\DateTimeInterface::RFC3339);
        }

        if (null !== $this->reservationMethod) {
            $providedOptions['reservationMethod'] = $this->reservationMethod->value;
        }

        if (null !== $this->minCapacity) {
            $providedOptions['minCapacity'] = $this->minCapacity;
        }

        if (null !== $this->reservationLengthMinutes && ReservationMethod::Hourly === $this->reservationMethod) {
            $providedOptions['reservationLengthMinutes'] = $this->reservationLengthMinutes;
        }

        if (null !== $this->amenityIds) {
            $providedOptions['amenityIds'] = $this->amenityIds;
        }

        return [
            'json' => $providedOptions,
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
