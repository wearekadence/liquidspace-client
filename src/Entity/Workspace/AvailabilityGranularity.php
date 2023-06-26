<?php

namespace LiquidSpace\Entity\Workspace;

enum AvailabilityGranularity: string
{
    case FifteenMinutes = 'min15';
    case ThirtyMinutes = 'min30';
    case OneHour = 'min60';
}
