<?php

namespace LiquidSpace\Entity\Venue;

enum ReservationMethod: int
{
    case Hourly = 0;
    case Daily = 1;
    case Monthly = 2;
}
