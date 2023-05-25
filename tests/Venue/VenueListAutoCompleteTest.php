<?php

namespace LiquidSpaceClient\Tests\Venue;

use LiquidSpaceClient\LiquidSpaceClient;
use PHPUnit\Framework\TestCase;
use LiquidSpaceClient\Request\VenueListAutoCompleteRequest;
use LiquidSpaceClient\Response\VenueListAutoCompleteResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class VenueListAutoCompleteTest extends TestCase
{
    public function testVenueListAutoComplete(): void
    {
        $expectedResponseData = [
            [
                "key" => "04637609-c1d5-4848-b34f-8e1ef83de14f",
                "value" => "London venue from bila",
            ],
            [
                "key" => "0b838865-6922-45d9-8bdf-578e317104c2",
                "value" => "LocationOutEshota",
            ],
        ];
        $mockResponseJson = json_encode($expectedResponseData, JSON_THROW_ON_ERROR);
        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['content-type' => 'application/json; charset=utf-8']
        ]);

        $client = new LiquidSpaceClient(new MockHttpClient([$mockResponse]), 'test');

        $request = new VenueListAutoCompleteRequest('search');

        $actualResponse = $client->request($request, VenueListAutoCompleteResponse::class);

        self::assertNotNull($actualResponse);
        self::assertSame('GET', $mockResponse->getRequestMethod());
        self::assertSame('https://ls-api-dev.azure-api.net/marketplace/api/venues/search?term=search', $mockResponse->getRequestUrl());
        self::assertCount(2, $actualResponse->venues);
        self::assertEquals('04637609-c1d5-4848-b34f-8e1ef83de14f', $actualResponse->venues[0]->id);
        self::assertEquals('London venue from bila', $actualResponse->venues[0]->name);
    }
}
