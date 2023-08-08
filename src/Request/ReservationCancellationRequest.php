<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\ReservationCancellationResponse;

class ReservationCancellationRequest implements RequestInterface
{
    /**
     * @param string                  $reservationId  Reservation GUID to cancel
     * @param \DateTimeImmutable|null $occurrenceDate Optional reservation occurrence date used to identify instance of recurring reservation
     * @param \DateTimeImmutable|null $fromDate       Optional date to cancel all occurrences from
     */
    public function __construct(
        private readonly string $reservationId,
        private readonly ?\DateTimeImmutable $occurrenceDate = null,
        private readonly ?\DateTimeImmutable $fromDate = null
    ) {
    }

    public static function getResponseClass(): string
    {
        return ReservationCancellationResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Delete;
    }

    public function getPath(): string
    {
        return sprintf(
            '/marketplace/api/reservations/%s',
            $this->reservationId,
        );
    }

    public function getOptions(): array
    {
        $optionalOptions = [];

        if ($this->occurrenceDate) {
            $optionalOptions['query']['occurrenceDate'] = $this->occurrenceDate->format(\DateTimeInterface::RFC3339);
        }

        if ($this->fromDate) {
            $optionalOptions['query']['applyDate'] = $this->fromDate->format(\DateTimeInterface::RFC3339);
        }

        return $optionalOptions;
    }

    public function requiresEnterpriseToken(): bool
    {
        return false;
    }
}
