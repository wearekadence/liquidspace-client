<?php

namespace LiquidSpace\Entity\Reservation;

enum ReservationStatus: int
{
    case Deleted = 0;
    case Completed = 1;
    case CancellationPolicy = 2;
    case Future = 3;
    case Active = 4;
}
