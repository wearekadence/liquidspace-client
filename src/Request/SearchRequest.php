<?php

namespace LiquidSpace\Request;

use LiquidSpace\Entity\Venue\ReservationMethod;
use LiquidSpace\Entity\Venue\SearchSourceType;
use LiquidSpace\Entity\Workspace\SpaceType;
use LiquidSpace\Response\SearchResponse;

class SearchRequest implements RequestInterface
{
    /**
     * @param SpaceType[] $spaceTypes
     */
    public function __construct(
        private readonly ?string $savedSearchId = null,
        private readonly ?string $address = null,
        private readonly ?array $spaceTypes = null,
        private readonly ?float $minPrice = null,
        private readonly ?float $maxPrice = null,
        private readonly ?string $venueId = null,
        private readonly ?string $venueGroupId = null,
        private readonly bool $isCurrentLocationSearch = false,
        private readonly ?SearchSourceType $sourceType = null,
        private readonly ?\DateTimeImmutable $startTime = null,
        private readonly ?ReservationMethod $reservationMethod = null,
        private readonly ?int $workspaceCapacity = null,
        private readonly bool $isFullTextSearch = false,
        private readonly ?int $reservationLengthMinutes = null,
    ) {
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

        if (null !== $this->savedSearchId) {
            $providedOptions['savedSearchId'] = $this->savedSearchId;
        }

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

        if (null !== $this->venueId) {
            $providedOptions['venueId'] = $this->venueId;
        }

        if (null !== $this->venueGroupId) {
            $providedOptions['venueGroupId'] = $this->venueGroupId;
        }

        $providedOptions['isCurrentLocationSearch'] = $this->isCurrentLocationSearch;

        if (null !== $this->sourceType) {
            $providedOptions['sourceType'] = $this->sourceType->value;
        }

        if (null !== $this->startTime) {
            $providedOptions['startTime'] = $this->startTime->format('c');
        }

        if (null !== $this->reservationMethod) {
            $providedOptions['reservationMethod'] = $this->reservationMethod->value;
        }

        if (null !== $this->workspaceCapacity) {
            $providedOptions['workspaceCapacity'] = $this->workspaceCapacity;
        }

        if (null !== $this->reservationLengthMinutes) {
            $providedOptions['reservationLengthMinutes'] = $this->reservationLengthMinutes;
        }

        $providedOptions['isFullTextSearch'] = $this->isFullTextSearch;

        return [
            'json' => $providedOptions,
        ];
    }
}
