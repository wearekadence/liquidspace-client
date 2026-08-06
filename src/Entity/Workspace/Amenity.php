<?php

namespace LiquidSpace\Entity\Workspace;

class Amenity
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly ?string $instruction;
    public readonly ?string $imageUrl;
    public readonly ?bool $paid;
    public readonly ?bool $isWorkspace;

    public function __construct(array $amenityData)
    {
        $this->name = $amenityData['name'];
        $this->description = $amenityData['description'] ?? null;
        $this->instruction = $amenityData['instruction'] ?? null;
        $this->imageUrl = $amenityData['imageUrl'] ?? null;
        $this->paid = $amenityData['paid'] ?? null;
        $this->isWorkspace = $amenityData['isWorkspace'] ?? null;
    }
}
