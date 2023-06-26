<?php

namespace LiquidSpace\Entity\Workspace;

enum WorkspaceAvailabilityState: int
{
    case Closed = 0;
    case Booked = 1;
    case Available = 2;
    case OwnBooking = 3;
}
