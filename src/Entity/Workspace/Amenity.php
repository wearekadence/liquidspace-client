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
        $this->description = $amenityData['description'];
        $this->instruction = $amenityData['instruction'];
        $this->imageUrl = $amenityData['imageUrl'];
        $this->paid = $amenityData['paid'];
        $this->isWorkspace = $amenityData['isWorkspace'];
    }
}
