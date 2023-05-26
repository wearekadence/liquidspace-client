<?php

namespace LiquidSpace\Entity\Venue;

enum ReservationMethod: int
{
    case SpecificTimePeriod = 0;
    case DayPass = 1;
    case Period = 2;
}
