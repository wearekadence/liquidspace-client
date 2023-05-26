<?php

namespace LiquidSpace\Entity\Reservation;

enum HourlyRequestToBookStatus: int
{
    case Unknown = 0;
    case Pending = 1;
    case Approved = 2;
    case CancelledByHost = 3;
    case CancelledByGuest = 4;
    case CancelledNoResponse = 5;
}
