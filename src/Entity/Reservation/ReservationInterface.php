<?php

namespace LiquidSpace\Entity\Reservation;

use LiquidSpace\Entity\Venue\ReservationMethod;
use LiquidSpace\Entity\Workspace\SpaceType;

interface ReservationInterface
{
    public function getId(): string;

    public function getVenueId(): string;

    public function getWorkspaceId(): string;

    public function getStartTime(): \DateTimeImmutable;

    public function getEndTime(): \DateTimeImmutable;

    public function getMethod(): ReservationMethod;

    public function getStatus(): ReservationStatus;

    public function getWorkspaceName(): string;

    /** @return SpaceType[] */
    public function getSpaceTypes(): array;
}
