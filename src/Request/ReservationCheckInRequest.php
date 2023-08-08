<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\ReservationCheckInResponse;

class ReservationCheckInRequest implements RequestInterface
{
    /**
     * @param string                  $reservationId  Reservation GUID to check into
     * @param \DateTimeImmutable|null $occurrenceDate Optional reservation occurrence date used to identify instance of recurring reservation
     */
    public function __construct(
        private readonly string $reservationId,
        private readonly ?\DateTimeImmutable $occurrenceDate = null
    ) {
    }

    public static function getResponseClass(): string
    {
        return ReservationCheckInResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Post;
    }

    public function getPath(): string
    {
        return sprintf(
            '/marketplace/api/reservations/%s/visit-state/checkin',
            $this->reservationId,
        );
    }

    public function getOptions(): array
    {
        $optionalOptions = [];

        if ($this->occurrenceDate) {
            $optionalOptions['query']['occurrenceDate'] = $this->occurrenceDate->format(\DateTimeInterface::RFC3339);
        }

        return $optionalOptions;
    }

    public function requiresEnterpriseToken(): bool
    {
        return false;
    }
}
