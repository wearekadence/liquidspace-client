<?php

namespace LiquidSpace\Entity\Reservation;

use LiquidSpace\Entity\Venue\ReservationMethod;
use LiquidSpace\Entity\Workspace\Amenity;
use LiquidSpace\Entity\Workspace\SpaceType;

/**
 * The following properties have been ignored for simplicity: costSummary, paymentHistory & hourlyRequestToBook.
 */
class Reservation implements ReservationInterface
{
    public readonly string $id;
    public readonly string $venueName;
    public readonly string $address;
    public readonly float $latitude;
    public readonly float $longitude;
    public readonly float $venueAverageRating;
    public readonly int $venueRatingCount;
    public readonly string $venueHostFirstName;
    public readonly string $venueHostLastName;
    public readonly ?string $venueHostPhone;
    public readonly ?string $reviewId;
    public readonly ?int $memberStars;
    public readonly ?string $memberReviewText;
    public readonly ?string $memberReviewName;
    public readonly bool $isToday;
    public readonly string $idForLink;
    public readonly ?\DateTimeImmutable $occurrenceDate;
    public readonly bool $deleted;
    public readonly \DateTimeImmutable $startTime;
    public readonly \DateTimeImmutable $endTime;
    public readonly ?\DateTimeImmutable $finishDate;
    public readonly ReservationStatus $status;
    public readonly ReservationState $reservationState;
    public readonly ReservationMethod $reservationMethod;
    public readonly BookingMethod $bookingMethod;
    public readonly string $ownerId;
    public readonly ReservationInvitee $organizer;
    public readonly string $title;
    public readonly CancellationPolicy $cancellationPolicy;
    public readonly array $amenities;
    public readonly array $invitees;
    public readonly ReservationOperations $reservationOperations;
    public readonly ReservationPrice $price;
    public readonly array $paymentHistory;
    public readonly string $venueId;
    public readonly string $workspaceId;
    public readonly string $workspaceName;
    public readonly ?string $pictureUrl;
    public readonly int $workspaceCapacity;
    public readonly string $workspaceUrl;
    public readonly string $spaceTypeForCapacity;
    public readonly bool $isAutoRenew;
    /** @var SpaceType[] */
    public readonly array $spaceTypes;

    public function __construct(array $reservationData)
    {
        $this->id = $reservationData['id'];
        $this->venueName = $reservationData['venueName'];
        $this->address = $reservationData['address'];
        $this->latitude = $reservationData['latitude'];
        $this->longitude = $reservationData['longitude'];
        $this->venueAverageRating = $reservationData['venueAverageRating'];
        $this->venueRatingCount = $reservationData['venueRatingCount'];
        $this->venueHostFirstName = $reservationData['venueHostName'];
        $this->venueHostLastName = $reservationData['venueHostLastName'];
        if (isset($reservationData['venueHostPhone'])) {
            $this->venueHostPhone = $reservationData['venueHostPhone'];
        }
        $this->reviewId = $reservationData['reviewId'] ?? null;
        $this->memberStars = $reservationData['memberStars'] ?? null;
        $this->memberReviewText = $reservationData['memberReviewText'] ?? null;
        $this->memberReviewName = $reservationData['memberReviewName'] ?? null;
        $this->isToday = $reservationData['isToday'];
        $this->idForLink = $reservationData['idForLink'];
        $this->occurrenceDate = $reservationData['occurrenceDate'] ? new \DateTimeImmutable($reservationData['occurrenceDate']) : null;
        $this->deleted = $reservationData['deleted'];
        $this->startTime = new \DateTimeImmutable($reservationData['startTime']);
        $this->endTime = new \DateTimeImmutable($reservationData['endTime']);
        $this->finishDate = $reservationData['finishDate'] ? new \DateTimeImmutable($reservationData['finishDate']) : null;
        $this->spaceTypes = SpaceType::decode($reservationData['spaceType']);

        $status = ReservationStatus::tryFrom($reservationData['status']);
        if (null === $status) {
            throw new \InvalidArgumentException('Invalid reservation status: '.$reservationData['status']);
        }
        $this->status = $status;

        $state = ReservationState::tryFrom($reservationData['reservationState']);
        if (null === $state) {
            throw new \InvalidArgumentException('Invalid reservation state: '.$reservationData['reservationState']);
        }
        $this->reservationState = $state;

        $method = ReservationMethod::tryFrom($reservationData['reservationMethod']);
        if (null === $method) {
            throw new \InvalidArgumentException('Invalid reservation method: '.$reservationData['reservationMethod']);
        }
        $this->reservationMethod = $method;

        $bookingMethod = BookingMethod::tryFrom($reservationData['bookingMethod']);
        if (null === $bookingMethod) {
            throw new \InvalidArgumentException('Invalid booking method: '.$reservationData['bookingMethod']);
        }
        $this->bookingMethod = $bookingMethod;

        $this->ownerId = $reservationData['ownerId'];
        $this->organizer = new ReservationInvitee($reservationData['organizer']);
        $this->title = $reservationData['title'];

        $cancellationPolicy = CancellationPolicy::tryFrom($reservationData['cancellationPolicy']);
        if (null === $cancellationPolicy) {
            throw new \InvalidArgumentException('Invalid cancellation policy: '.$reservationData['cancellationPolicy']);
        }
        $this->cancellationPolicy = $cancellationPolicy;

        $this->invitees = \array_map(
            fn (array $inviteeData) => new ReservationInvitee($inviteeData),
            $reservationData['invitees']
        );

        $this->amenities = \array_map(
            fn (array $amenityData) => new Amenity($amenityData),
            $reservationData['amenities']
        );

        $this->reservationOperations = new ReservationOperations($reservationData['reservationOperations']);
        $this->price = new ReservationPrice($reservationData['price']);
        $this->paymentHistory = $reservationData['paymentHistory'];
        $this->venueId = $reservationData['venueId'];
        $this->workspaceId = $reservationData['workspaceId'];
        $this->workspaceName = $reservationData['workspaceName'];
        $this->pictureUrl = $reservationData['picture'];
        $this->workspaceCapacity = $reservationData['workspaceCapacity'];
        $this->workspaceUrl = $reservationData['workspaceUrl'];
        $this->spaceTypeForCapacity = $reservationData['spaceTypeForCapacity'];
        $this->isAutoRenew = $reservationData['isAutoRenew'];
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
        return $this->workspaceName;
    }

    /**
     * @return SpaceType[]
     */
    public function getSpaceTypes(): array
    {
        return $this->spaceTypes;
    }
}
