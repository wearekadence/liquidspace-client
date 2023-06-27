<?php

namespace LiquidSpace\Entity\Reservation;

use LiquidSpace\Entity\Venue\ReservationMethod;

/**
* "organizer": {
    * "firstName": "Jordan",
    * "lastName": "de Laune",
    * "imageUrl": "https://dev-picture.liquidspaceapp.com/Index?emptyImageUrl=https%3a%2f%2fdev.liquidspaceapp.com%2fContent%2fImages%2fliquid-holder.jpg&etag=638234640234851305&aux=%2f%2frdporI%2fOll98qm7XoplPB3rj%2fjqwzRJWSFnDwu1pUbyM0Pf4VkQJ9leUGNfdfsky1eWZ5dk3G8LOgN4nMiVg%3d%3d",
    * "email": "jordan@kadence.co",
    * "spaceName": null
* },
* "amenities": [],
* "invitees": [],
* "reservationOperations": {
    * "editAllowed": false,
    * "cancelAllowed": false,
    * "checkInAllowed": false,
    * "checkOutAllowed": false,
    * "doNotRenewAllowed": false,
    * "extendAllowed": false,
    * "inviteAllowed": false
* },
* "price": {
    * "amount": 0.0,
    * "currency": 3,
    * "currencyPrefix": "GBP £",
    * "currencyRate": 0.78598
* },
* "costSummary": {
    * "total": 5.0,
    * "salesPrice": 5.0,
    * "salesTax": 0.0,
    * "setupFee": 0.0,
    * "deposit": 0.0,
    * "amountPaid": 0.0,
    * "incidentalsTotal": 0.0,
    * "incidentalsTax": 0.0,
    * "paymentMethod": "Venue Billed",
    * "currencyPrefix": "GBP £",
    * "isLastMonthIncidentalsPayment": false,
    * "refundType": null
* },
* "paymentHistory": [],
* "hourlyRequestToBook": null,
 */
class Reservation
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
    public readonly string $venueHostPhone;
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
    //public readonly string $organizer;
    public readonly string $title;
    public readonly CancellationPolicy $cancellationPolicy;
//    public readonly array $amenities;
//    public readonly array $invitees;
//    public readonly ReservationOperations $reservationOperations;
//    public readonly Price $price;
//    public readonly CostSummary $costSummary;
    public readonly array $paymentHistory;
    public readonly string $venueId;
    public readonly string $workspaceId;
    public readonly string $workspaceName;
    public readonly ?string $pictureUrl;
    public readonly int $workspaceCapacity;
    public readonly string $workspaceUrl;
    public readonly string $spaceTypeForCapacity;
    //public readonly ?HourlyRequestToBook $hourlyRequestToBook;
    public readonly bool $isAutoRenew;

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
        $this->venueHostPhone = $reservationData['venueHostPhone'];
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
        //$this->organizer = $reservationData['organizer'];
        $this->title = $reservationData['title'];

        $cancellationPolicy = CancellationPolicy::tryFrom($reservationData['cancellationPolicy']);
        if (null === $cancellationPolicy) {
            throw new \InvalidArgumentException('Invalid cancellation policy: '.$reservationData['cancellationPolicy']);
        }
        $this->cancellationPolicy = $cancellationPolicy;

        //$this->amenities = $reservationData['amenities'];
        //$this->invitees = $reservationData['invitees'];
        //$this->reservationOperations = new ReservationOperations($reservationData['reservationOperations']);
        //$this->price = new Price($reservationData['price']);
        //$this->costSummary = new CostSummary($reservationData['costSummary']);
        $this->paymentHistory = $reservationData['paymentHistory'];
        $this->venueId = $reservationData['venueId'];
        $this->workspaceId = $reservationData['workspaceId'];
        $this->workspaceName = $reservationData['workspaceName'];
        $this->pictureUrl = $reservationData['picture'];
        $this->workspaceCapacity = $reservationData['workspaceCapacity'];
        $this->workspaceUrl = $reservationData['workspaceUrl'];
        $this->spaceTypeForCapacity = $reservationData['spaceTypeForCapacity'];
        //$this->hourlyRequestToBook = $reservationData['hourlyRequestToBook'];
        $this->isAutoRenew = $reservationData['isAutoRenew'];
    }
}
