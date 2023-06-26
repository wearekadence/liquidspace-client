<?php

namespace LiquidSpace\Tests\Venue;

use LiquidSpace\Client;
use LiquidSpace\Entity\Workspace\SpaceType;
use LiquidSpace\Request\VenueRequest;
use LiquidSpace\Response\VenueResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetVenueTest extends TestCase
{
    public function testGetVenue(): void
    {
        $expectedResponseData = [
            'id' => '04637609-c1d5-4848-b34f-8e1ef83de14f',
            'latitude' => 51.5013,
            'longitude' => -0.11602,
            'name' => 'London venue from bila',
            'address' => '1 Addington Street, Lambeth, London SE1 7RY',
            'simpleAddress1' => '105A Euston Street',
            'simpleAddress2' => null,
            'city' => 'London',
            'state' => 'GB-NYK',
            'zip' => 'NW1 2EW',
            'country' => 'UK',
            'timeZone' => [
                'windowsId' => 'GMT Standard Time',
                'territory' => '001',
                'tzdbIds' => [
                    'Europe/London',
                ],
            ],
            'description' => 'A great building',
            'imageUrls' => [
               'https://dev.liquidspaceapp.com/streetview?location=51.5013,-0.11602&size=450x300',
            ],
            'url' => '/uk/london/lambeth/london-venue-from-bila',
            'averageRating' => 1.5,
            'ratingCount' => 3,
            'healthAndSafetyPolicy' => null,
            'hasLiquidspacePro' => false,
            'hourlyWorkspaces' => [
                [
                    'id' => '3855d73e-230c-4129-9f62-607575ae94a4',
                    'name' => 'Park Plaza County Hall 2',
                    'url' => '/uk/london/lambeth/london-venue-from-bila/park-plaza-county-hall-2',
                    'spaceType' => 1,
                    'spaceTypeFormatted' => 'Meeting Space',
                    'capacity' => 20,
                    'pricesFormatted' => 'GBP £200/hour',
                ],
                [
                    'id' => '7fb56c9d-c69a-4654-9049-a23d1ef27843',
                    'name' => 'Park Plaza County Hall daypass',
                    'url' => '/uk/london/lambeth/london-venue-from-bila/park-plaza-county-hall-daypass',
                    'spaceType' => 1,
                    'spaceTypeFormatted' => 'Meeting Space',
                    'capacity' => 1,
                    'pricesFormatted' => 'GBP £300/day-pass',
                ],
            ],
            'venueMapImageUrl' => 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/-0.11602,51.5013,15,0.00,0.00/288x216@2x?access_token=pk.eyJ1IjoibGlxdWlkc3BhY2UiLCJhIjoiY2psODdsZm0yMGNyazNxbWt6Njc1OGR3eiJ9._OSdF9FwNYZ85hDNK96D7Q',
            'defaultBookingMethod' => 3,
            'defaultBookingMethodHourly' => 2
        ];

        // @phpstan-ignore-next-line
        $mockResponse = new JsonMockResponse($expectedResponseData, [
            'http_code' => 200,
            'response_headers' => ['content-type' => 'application/json; charset=utf-8']
        ]);

        $client = $this->createClient(new MockHttpClient([$mockResponse]));

        $request = new VenueRequest('04637609-c1d5-4848-b34f-8e1ef83de14f');

        $actualResponse = $client->request($request, VenueResponse::class);

        self::assertNotNull($actualResponse);
        self::assertSame('GET', $mockResponse->getRequestMethod());
        self::assertSame('https://ls-api-dev.azure-api.net/marketplace/api/venues/04637609-c1d5-4848-b34f-8e1ef83de14f', $mockResponse->getRequestUrl());
        self::assertEquals('04637609-c1d5-4848-b34f-8e1ef83de14f', $actualResponse->venue->id);
        self::assertEquals(51.5013, $actualResponse->venue->latitude);
        self::assertEquals(-0.11602, $actualResponse->venue->longitude);
        self::assertEquals('London venue from bila', $actualResponse->venue->name);
        self::assertEquals('1 Addington Street, Lambeth, London SE1 7RY', $actualResponse->venue->address);
        self::assertEquals('A great building', $actualResponse->venue->description);
        self::assertCount(1, $actualResponse->venue->imageUrls);
        self::assertEquals('https://dev.liquidspaceapp.com/streetview?location=51.5013,-0.11602&size=450x300', $actualResponse->venue->imageUrls[0]);
        self::assertEquals('/uk/london/lambeth/london-venue-from-bila', $actualResponse->venue->url);
        self::assertEquals(1.5, $actualResponse->venue->averageRating);
        self::assertEquals(3, $actualResponse->venue->ratingCount);
        self::assertNull($actualResponse->venue->healthAndSafetyPolicy);
        self::assertFalse($actualResponse->venue->hasLiquidSpacePro);
        self::assertEquals('https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/-0.11602,51.5013,15,0.00,0.00/288x216@2x?access_token=pk.eyJ1IjoibGlxdWlkc3BhY2UiLCJhIjoiY2psODdsZm0yMGNyazNxbWt6Njc1OGR3eiJ9._OSdF9FwNYZ85hDNK96D7Q', $actualResponse->venue->mapImageUrl);
        self::assertCount(2, $actualResponse->venue->workspaces);
        self::assertEquals('3855d73e-230c-4129-9f62-607575ae94a4', $actualResponse->venue->workspaces[0]->id);
        self::assertEquals('Park Plaza County Hall 2', $actualResponse->venue->workspaces[0]->name);
        self::assertEquals('/uk/london/lambeth/london-venue-from-bila/park-plaza-county-hall-2', $actualResponse->venue->workspaces[0]->url);
        self::assertEquals([SpaceType::Meeting], $actualResponse->venue->workspaces[0]->spaceTypes);
        self::assertEquals('Meeting Space', $actualResponse->venue->workspaces[0]->spaceTypeFormatted);
        self::assertEquals(20, $actualResponse->venue->workspaces[0]->capacity);
        self::assertEquals('GBP £200/hour', $actualResponse->venue->workspaces[0]->pricesFormatted);
    }

    public function testGetVenueNotFound(): void
    {
        $mockResponse = new MockResponse('', [
            'http_code' => 404,
            'response_headers' => ['content-type' => 'application/json; charset=utf-8']
        ]);

        $client = $this->createClient(new MockHttpClient([$mockResponse]));

        $request = new VenueRequest('04637609-c1d5-4848-b34f-8e1ef83de14f');

        $actualResponse = $client->request($request, VenueResponse::class);

        self::assertSame('GET', $mockResponse->getRequestMethod());
        self::assertSame('https://ls-api-dev.azure-api.net/marketplace/api/venues/04637609-c1d5-4848-b34f-8e1ef83de14f', $mockResponse->getRequestUrl());
        self::assertNull($actualResponse);
    }

    private function createClient(?HttpClientInterface $httpClient = null): Client
    {
        return new Client(
            $httpClient ?? new MockHttpClient(),
            $this->createMock(CacheInterface::class),
            'subscriptionKey',
            'clientId',
            'clientSecret',
        );
    }
}
