<?php

namespace Client\Entity\Workspace;

enum SpaceType: int
{
    case Any = 0;
    case Meeting = 1;
    case Office = 2;
    case Desk = 4;
    case Training = 8;
    case TeamSpace = 16;
    case PrivateOffice = 32;
    case EventSpace = 64;
    case OpenDesk = 128;
    case DedicatedDesk = 256;
    case Membership = 512;
}
