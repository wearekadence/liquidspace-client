<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Enterprise\EnterpriseReservation;
use Symfony\Contracts\HttpClient\ResponseInterface;

class EnterpriseAccountListReservationsResponse
{
    /** @var EnterpriseReservation[] */
    public readonly array $reservations;

    public function __construct(ResponseInterface $response)
    {
        $reservationsData = $response->toArray();

        $this->reservations = array_map(
            fn (array $reservationData) => new EnterpriseReservation($reservationData),
            $reservationsData,
        );
    }
}
