<?php

namespace LiquidSpace\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;

class ReservationCancellationResponse
{
    public readonly bool $success;

    public function __construct(ResponseInterface $response)
    {
        $content = $response->toArray();

        $this->success = $content['success'];
    }
}
