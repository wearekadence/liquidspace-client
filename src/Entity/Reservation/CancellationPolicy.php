<?php

namespace LiquidSpace\Entity\Reservation;

enum CancellationPolicy: int
{
    case FlexibleCancellation = 0;
    case NoCancellation = 1;
    case Moderate7 = 2;
    case Moderate14 = 3;
    case Extended = 4;
}
