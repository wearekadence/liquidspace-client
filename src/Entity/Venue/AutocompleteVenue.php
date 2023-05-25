<?php

namespace LiquidSpace\Entity\Venue;

class AutocompleteVenue
{
    public readonly string $id;
    public readonly string $name;

    public function __construct(array $venue)
    {
        $this->id = $venue['key'];
        $this->name = $venue['value'];
    }
}
