<?php

namespace LiquidSpace\Request;

use LiquidSpace\Response\ReservationCancellationResponse;

class ReservationRequest implements RequestInterface
{
    /**
     * @param string $reservationId Reservation GUID to get
     */
    public function __construct(
        private readonly string $reservationId,
    ) {
    }

    public static function getResponseClass(): string
    {
        return ReservationCancellationResponse::class;
    }

    public static function getMethod(): HttpMethod
    {
        return HttpMethod::Get;
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
        return [];
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
