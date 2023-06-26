<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Workspace\WorkspaceAvailability;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VenueAvailabilityResponse
{
    /** @var WorkspaceAvailability[] */
    public readonly array $workspaces;

    public function __construct(ResponseInterface $response)
    {
        $content = $response->toArray();

        $workspaces = [];
        foreach ($content as $workspaceAvailability) {
            $workspaces[] = new WorkspaceAvailability($workspaceAvailability);
        }
        $this->workspaces = $workspaces;
    }
}
