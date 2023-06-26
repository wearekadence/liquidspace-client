<?php

namespace LiquidSpace\Entity\Workspace;

class WorkspaceAvailabilityPeriod
{
    public readonly string $timePeriod;
    public readonly WorkspaceAvailabilityState $availability;

    public function __construct(array $availabilityData)
    {
        $availabilityState = WorkspaceAvailabilityState::tryFrom($availabilityData['state']);
        if (null === $availabilityState) {
            throw new \InvalidArgumentException('Invalid availability state: '.$availabilityData['state']);
        }

        $this->timePeriod = $availabilityData['time'];
        $this->availability = $availabilityState;
    }
}
