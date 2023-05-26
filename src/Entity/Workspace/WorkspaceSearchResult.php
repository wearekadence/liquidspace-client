<?php

namespace LiquidSpace\Entity\Workspace;

class WorkspaceSearchResult
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $imageUrl;
    public readonly string $url;
    public readonly float $price;
    public readonly string $priceAmount;
    public readonly string $priceDescription;
    public readonly SpaceType $spaceType;
    public readonly string $spaceTypeFormatted;
    public readonly int $capacity;

    public function __construct(array $workspaceData)
    {
        $spaceType = SpaceType::tryFrom($workspaceData['spaceType']);
        if (null === $spaceType) {
            throw new \InvalidArgumentException('Invalid space type: '.$workspaceData['spaceType']);
        }

        $this->id = $workspaceData['id'];
        $this->name = $workspaceData['name'];
        $this->imageUrl = $workspaceData['imgUrl'];
        $this->url = $workspaceData['url'];
        $this->price = $workspaceData['price'];
        $this->priceAmount = $workspaceData['priceAmount'];
        $this->priceDescription = $workspaceData['priceDescription'];
        $this->spaceType = $spaceType;
        $this->spaceTypeFormatted = $workspaceData['spaceTypeFormatted'];
        $this->capacity = $workspaceData['capacity'];
    }
}