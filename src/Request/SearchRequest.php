<?php

namespace LiquidSpace\Request;

use LiquidSpace\Entity\Venue\ReservationMethod;
use LiquidSpace\Entity\Workspace\SpaceType;
use LiquidSpace\Response\SearchResponse;

class SearchRequest implements RequestInterface
{
    private readonly ?int $reservationLengthMinutes;

    /**
     * @param SpaceType[] $spaceTypes
     */
    public function __construct(
        private readonly ?string $address = null,
        private readonly ?\DateTimeImmutable $startTime = null,
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
        return '/marketplace/api/search';
    }

    public function getOptions(): array
    {
        $providedOptions = [];

        if (null !== $this->address) {
            $providedOptions['address'] = $this->address;
        }

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

        if (null !== $this->reservationLengthMinutes) {
            $providedOptions['reservationLengthMinutes'] = $this->reservationLengthMinutes;
        }

        $providedOptions['amenityIds'] = $this->amenityIds;

        return [
            'json' => $providedOptions,
        ];
    }

    public function requiresEnterpriseToken(): bool
    {
        return false;
    }
}
