<?php

namespace LiquidSpace\Response;

use LiquidSpace\Entity\Workspace\Workspace;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VenueListWorkspacesResponse
{
    /** @var Workspace[] */
    public readonly array $workspaces;

    public function __construct(ResponseInterface $response)
    {
        $content = $response->toArray();

        $workspaces = [];
        foreach ($content as $workspace) {
            $workspaces[] = new Workspace($workspace);
        }
        $this->workspaces = $workspaces;
    }
}
