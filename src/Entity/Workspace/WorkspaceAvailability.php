<?php

namespace LiquidSpace\Entity\Workspace;

enum WorkspaceAvailability: int
{
    case Closed = 0;
    case Booked = 1;
    case Available = 2;
    case OwnBooking = 3;
}
