<?php

namespace LiquidSpace\Entity\Workspace;

use LiquidSpace\Entity\Venue\ReservationMethod;

class Workspace
{
    public readonly string $id;
    public readonly ?string $name;
    public readonly string $url;
    /** @var SpaceType[] */
    public readonly array $spaceTypes;
    public readonly string $spaceTypeFormatted;
    public readonly int $capacity;
    public readonly string $pricesFormatted;
    public readonly ReservationMethod $reservationMethod;
    /** @var string[] */
    public readonly array $imageUrls;

    public function __construct(array $workspaceData)
    {
        $method = ReservationMethod::tryFrom($workspaceData['reservationMethod']);
        if (null === $method) {
            throw new \InvalidArgumentException('Invalid reservation method: '.$workspaceData['reservationMethod']);
        }
        $this->reservationMethod = $method;

        $this->id = $workspaceData['id'];

        if (isset($workspaceData['name'])) {
            $this->name = $workspaceData['name'];
        }

        $this->spaceTypes = SpaceType::decode($workspaceData['spaceType']);
        $this->url = $workspaceData['url'];
        $this->spaceTypeFormatted = $workspaceData['spaceTypeFormatted'];
        $this->capacity = $workspaceData['capacity'];
        $this->pricesFormatted = $workspaceData['pricesFormatted'];
        $this->imageUrls = $workspaceData['imageUrls'];
    }
}
