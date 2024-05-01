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
        if (isset($amenityData['description'])) {
            $this->description = $amenityData['description'];
        }
        if (isset($amenityData['instruction'])) {
            $this->instruction = $amenityData['instruction'];
        }
        if (isset($amenityData['imageUrl'])) {
            $this->imageUrl = $amenityData['imageUrl'];
        }
        if (isset($amenityData['paid'])) {
            $this->paid = $amenityData['paid'];
        }
        if (isset($amenityData['isWorkspace'])) {
            $this->isWorkspace = $amenityData['isWorkspace'];
        }
    }
}
