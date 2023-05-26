<?php

namespace LiquidSpace\Entity\Workspace;

enum SpaceType: int
{
    case Any = 0;
    case Meeting = 1;
    case Office = 2;
    case Desk = 4;
    case Training = 8;
    case TeamSpace = 16;
    case PrivateOffice = 32;
    case EventSpace = 64;
    case OpenDesk = 128;
    case DedicatedDesk = 256;
    case Membership = 512;

    /**
     * @return SpaceType[]
     */
    public static function decode(int $value): array
    {
        $binary = decbin($value);
        $digits = \str_split(\strrev($binary));

        $types = [];
        foreach ($digits as $index => $digit) {
            if (0 === $index && $digit === '0' && 1 === count($digits)) {
                $types[] = SpaceType::Any;
            } elseif (0 === $index && $digit === '1') {
                $types[] = SpaceType::Meeting;
            } elseif (1 === $index && $digit === '1') {
                $types[] = SpaceType::Office;
            } elseif (2 === $index && $digit === '1') {
                $types[] = SpaceType::Desk;
            } elseif (3 === $index && $digit === '1') {
                $types[] = SpaceType::Training;
            } elseif (4 === $index && $digit === '1') {
                $types[] = SpaceType::TeamSpace;
            } elseif (5 === $index && $digit === '1') {
                $types[] = SpaceType::PrivateOffice;
            } elseif (6 === $index && $digit === '1') {
                $types[] = SpaceType::EventSpace;
            } elseif (7 === $index && $digit === '1') {
                $types[] = SpaceType::OpenDesk;
            } elseif (8 === $index && $digit === '1') {
                $types[] = SpaceType::DedicatedDesk;
            } elseif (9 === $index && $digit === '1') {
                $types[] = SpaceType::Membership;
            }
        }

        return $types;
    }

    /**
     * @param SpaceType[] $spaceTypes
     */
    public static function encode(array $spaceTypes): int
    {
        if (\in_array(SpaceType::Any, $spaceTypes, true)) {
            return 0;
        }

        $int = 0;
        foreach ($spaceTypes as $spaceType) {
            $int += $spaceType->value;
        }

        return $int;
    }
}
