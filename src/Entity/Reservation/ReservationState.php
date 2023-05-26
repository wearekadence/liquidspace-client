<?php

namespace LiquidSpace\Entity\Reservation;

enum ReservationState: int
{
    case Future = 0;
    case UpcomingSoon = 1;
    case CheckedIn = 2;
    case CheckedOut = 3;
    case RunningLate = 4;
    case OnMyWay = 5;
    case Declined = 6;
    case Extended = 7;
    case None = 100;
}
