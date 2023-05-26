<?php

namespace LiquidSpace\Tests\Entity;

use LiquidSpace\Entity\Workspace\SpaceType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SpaceTypeDecodeTest extends TestCase
{
    public static function spaceTypesDataProvider(): array
    {
        return [
            'Any' => [0, [SpaceType::Any]],
            'Meeting' => [1, [SpaceType::Meeting]],
            'Office' => [2, [SpaceType::Office]],
            'Desk' => [4, [SpaceType::Desk]],
            'Training' => [8, [SpaceType::Training]],
            'Team Space' => [16, [SpaceType::TeamSpace]],
            'Private Office' => [32, [SpaceType::PrivateOffice]],
            'Event Space' => [64, [SpaceType::EventSpace]],
            'Open Desk' => [128, [SpaceType::OpenDesk]],
            'Dedicated Desk' => [256, [SpaceType::DedicatedDesk]],
            'Membership' => [512, [SpaceType::Membership]],
            'Desk & Event Space' => [68, [SpaceType::Desk, SpaceType::EventSpace]],
            'Office & Membership' => [514, [SpaceType::Office, SpaceType::Membership]],
        ];
    }

    /**
     * @param SpaceType[] $expectedTypes
     */
    #[dataProvider('spaceTypesDataProvider')]
    public function testSpaceTypesIntDecode(int $int, array $expectedTypes): void
    {
        $types = SpaceType::decode($int);

        self::assertEquals($expectedTypes, $types);
    }

    /**
     * @param SpaceType[] $spaceTypes
     */
    #[dataProvider('spaceTypesDataProvider')]
    public function testSpaceTypesDecimalEncode(int $expectedInt, array $spaceTypes): void
    {
        $int = SpaceType::encode($spaceTypes);

        self::assertEquals($expectedInt, $int);
    }
}