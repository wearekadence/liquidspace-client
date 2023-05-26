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
    /** @var SpaceType[] */
    public readonly array $spaceTypes;
    public readonly string $spaceTypeFormatted;
    public readonly int $capacity;

    public function __construct(array $workspaceData)
    {
        $this->id = $workspaceData['id'];
        $this->name = $workspaceData['name'];
        $this->spaceTypes = SpaceType::decode($workspaceData['spaceType']);
        $this->imageUrl = $workspaceData['imgUrl'];
        $this->url = $workspaceData['url'];
        $this->price = $workspaceData['price'];
        $this->priceAmount = $workspaceData['priceAmount'];
        $this->priceDescription = $workspaceData['priceDescription'];
        $this->spaceTypeFormatted = $workspaceData['spaceTypeFormatted'];
        $this->capacity = $workspaceData['capacity'];
    }
}