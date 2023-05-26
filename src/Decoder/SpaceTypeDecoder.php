<?php

namespace LiquidSpace\Decoder;

final class SpaceTypeDecoder
{
    public static function decode(int $value): string
    {
        switch ($value) {
            case -1:
                return 'None';
            case 0:
                return 'Pending';
            case 1:
                return 'Active';
            default:
                throw new \InvalidArgumentException('Invalid value for enum SpaceType');
        }
    }
}