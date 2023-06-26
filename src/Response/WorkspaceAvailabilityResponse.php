<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Workspace\WorkspaceAvailabilityPeriod;
use LiquidSpace\Entity\Workspace\WorkspaceLocalAvailabilityPeriod;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WorkspaceAvailabilityResponse
{
    /** @var WorkspaceAvailabilityPeriod[] */
    public readonly array $periods;

    public function __construct(ResponseInterface $response)
    {
        $content = $response->toArray();

        $periods = [];
        foreach ($content as $period) {
            $periods[] = new WorkspaceAvailabilityPeriod($period);
        }
        $this->periods = $periods;
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
        foreach ($this->periods as $period) {
            $parts = \explode(':', $period->timePeriod);
            $localizedPeriod['datetime'] = $dateTime->setTime((int) $parts[0], (int) $parts[1], (int) $parts[2]);
            $localizedPeriod['state'] = $period->availability->value;
            $localizedPeriods[] = new WorkspaceLocalAvailabilityPeriod($localizedPeriod);
        }

        return $localizedPeriods;
    }
}
