<?php

namespace LiquidSpace\Entity\Workspace;

class WorkspaceAvailability
{
    public readonly Workspace $workspace;
    public readonly array $availabilityPeriods;

    public function __construct(array $availabilityData)
    {
        $this->workspace = new Workspace($availabilityData['workspace']);
        $this->availabilityPeriods = array_map(
            fn (array $availabilityPeriodData) => new WorkspaceAvailabilityPeriod($availabilityPeriodData),
            $availabilityData['timeChunkStates']
        );
    }

    /**
     * If you want the availability periods as DateTimeImmutable objects, pass in the date you want the periods to
     * be applied to. The DateTimeImmutable object should be given in the local timezone of the venue.
     *
     * @param \DateTimeImmutable $dateTime Should be given in the local timezone of the venue
     *
     * @return WorkspaceLocalAvailabilityPeriod[]
     */
    public function getPeriodsForDate(\DateTimeImmutable $dateTime): array
    {
        $localizedPeriods = [];
        foreach ($this->availabilityPeriods as $period) {
            $parts = \explode(':', $period->timePeriod);
            $localizedPeriod['datetime'] = $dateTime->setTime((int) $parts[0], (int) $parts[1], (int) $parts[2]);
            $localizedPeriod['state'] = $period->availability->value;
            $localizedPeriods[] = new WorkspaceLocalAvailabilityPeriod($localizedPeriod);
        }

        return $localizedPeriods;
    }
}
