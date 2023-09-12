<?php

namespace LiquidSpace\Entity\Enterprise;

enum ReservationStatusFilter: int
{
    /*
     * Cancelled
     */
    case Deleted = 1;

    /*
     * Completed (in the past)
     */
    case Completed = 2;

    /*
     * Cancelled, but outside the cancellation policy
     */
    case CancellationPolicy = 4;

    /*
     * Booked (in the future)
     */
    case Future = 8;

    /*
     * Booked, currently within the start and end time
     */
    case Active = 16;

    /*
     * All
     */
    case All = 255;
}
