<?php

namespace LiquidSpace\Entity\Reservation;

enum BookingMethod: int
{
    case NotAvailable = 0;
    case InquireNow = 1;
    case BookItNow = 2;
    case RequestToBook = 3;
}
