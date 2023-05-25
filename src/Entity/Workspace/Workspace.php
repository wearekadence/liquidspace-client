<?php

namespace LiquidSpaceClient\Entity\Workspace;

class Workspace
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $url;
    public readonly SpaceType $spaceType;
    public readonly string $spaceTypeFormatted;
    public readonly int $capacity;
    public readonly string $pricesFormatted;

    public function __construct(array $workspaceData)
    {
        $spaceType = SpaceType::tryFrom($workspaceData['spaceType']);
        if (null === $spaceType) {
            throw new \InvalidArgumentException('Invalid space type: '.$workspaceData['spaceType']);
        }

        $this->id = $workspaceData['id'];
        $this->name = $workspaceData['name'];
        $this->url = $workspaceData['url'];
        $this->spaceType = $spaceType;
        $this->spaceTypeFormatted = $workspaceData['spaceTypeFormatted'];
        $this->capacity = $workspaceData['capacity'];
        $this->pricesFormatted = $workspaceData['pricesFormatted'];
    }
}
