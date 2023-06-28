<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Reservation\Reservation;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ReservationResponse
{
    public readonly Reservation $reservation;

    public function __construct(ResponseInterface $response)
    {
        $content = $response->toArray();

        $this->reservation = new Reservation($content);
    }
}
