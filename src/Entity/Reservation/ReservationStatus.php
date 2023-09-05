<?php

namespace LiquidSpace\Entity\Reservation;

enum ReservationStatus: int
{
    /**
     * Cancelled
     */
    case Deleted = 0;

    /**
     * Completed (in the past)
     */
    case Completed = 1;

    /**
     * Cancelled, but outside the cancellation policy
     */
    case CancellationPolicy = 2;

    /**
     * Booked (in the future)
     */
    case Future = 3;

    /**
     * Booked, currently within the start and end time
     */
    case Active = 4;
}
