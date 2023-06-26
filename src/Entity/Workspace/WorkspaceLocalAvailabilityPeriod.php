<?php

namespace LiquidSpace\Entity\Workspace;

class WorkspaceLocalAvailabilityPeriod
{
    public readonly \DateTimeImmutable $timePeriod;
    public readonly WorkspaceAvailability $availability;

    public function __construct(array $availabilityData)
    {
        $availabilityState = WorkspaceAvailability::tryFrom($availabilityData['state']);
        if (null === $availabilityState) {
            throw new \InvalidArgumentException('Invalid availability state: '.$availabilityData['state']);
        }

        $this->timePeriod = $availabilityData['datetime'];
        $this->availability = $availabilityState;
    }
}