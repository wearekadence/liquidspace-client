<?php

namespace LiquidSpace\Entity\Enterprise;

use LiquidSpace\Entity\Reservation\ReservationInterface;
use LiquidSpace\Entity\Reservation\ReservationPrice;
use LiquidSpace\Entity\Reservation\ReservationStatus;
use LiquidSpace\Entity\Venue\ReservationMethod;
use LiquidSpace\Entity\Workspace\SpaceType;

class EnterpriseReservation implements ReservationInterface
{
    public readonly \DateTimeImmutable $startTime;
    public readonly \DateTimeImmutable $endTime;
    public readonly string $memberId;
    public readonly string $memberFullName;
    public readonly string $memberEmail;
    public readonly string $teamId;
    public readonly string $teamName;
    public readonly string $location;
    public readonly string $venueId;
    public readonly string $venueName;
    public readonly string $spaceName;
    /** @var SpaceType[] */
    public readonly array $spaceTypes;
    public readonly string $workspaceId;
    public readonly ReservationPrice $price;
    public readonly ReservationMethod $reservationMethod;
    public readonly ReservationStatus $status;
    public readonly PaymentProviderType $paymentProvider;
    public readonly string $id;
    public readonly string $idForLink;
    public readonly bool $isOutOfBudget;

    public function __construct(array $reservationData)
    {
        $this->startTime = new \DateTimeImmutable($reservationData['startDate']);
        $this->endTime = new \DateTimeImmutable($reservationData['endDate']);
        $this->memberId = $reservationData['memberId'];
        $this->memberFullName = $reservationData['memberFullName'];
        $this->memberEmail = $reservationData['memberEmail'];
        $this->teamId = $reservationData['teamId'];
        $this->teamName = $reservationData['teamName'];
        $this->location = $reservationData['location'];
        $this->venueId = $reservationData['venueId'];
        $this->venueName = $reservationData['venueName'];
        $this->spaceName = $reservationData['spaceName'];
        $this->spaceTypes = SpaceType::decode($reservationData['spaceType']);
        $this->workspaceId = $reservationData['workspaceId'];
        $this->price = new ReservationPrice($reservationData['cost']);

        $method = ReservationMethod::tryFrom($reservationData['reservationType']);
        if (null === $method) {
            throw new \InvalidArgumentException('Invalid reservation method: '.$reservationData['reservationType']);
        }
        $this->reservationMethod = $method;

        $status = ReservationStatus::tryFrom($reservationData['status']);
        if (null === $status) {
            throw new \InvalidArgumentException('Invalid reservation status: '.$reservationData['status']);
        }
        $this->status = $status;

        $paymentProvider = PaymentProviderType::tryFrom($reservationData['paymentProvider']);
        if (null === $paymentProvider) {
            throw new \InvalidArgumentException('Invalid payment provider: '.$reservationData['paymentProvider']);
        }
        $this->paymentProvider = $paymentProvider;

        $this->id = $reservationData['reservationId'];
        $this->idForLink = $reservationData['idForLink'];
        $this->isOutOfBudget = $reservationData['isOutOfBudget'];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getVenueId(): string
    {
        return $this->venueId;
    }

    public function getWorkspaceId(): string
    {
        return $this->workspaceId;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): \DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getMethod(): ReservationMethod
    {
        return $this->reservationMethod;
    }

    public function getStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function getWorkspaceName(): string
    {
        return $this->spaceName;
    }

    /**
     * @return SpaceType[]
     */
    public function getSpaceTypes(): array
    {
        return $this->spaceTypes;
    }
}
