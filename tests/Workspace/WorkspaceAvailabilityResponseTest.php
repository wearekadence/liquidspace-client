<?php

namespace LiquidSpace\Tests\Workspace;

use LiquidSpace\Client;
use LiquidSpace\Entity\Workspace\AvailabilityGranularity;
use LiquidSpace\Entity\Workspace\WorkspaceLocalAvailabilityPeriod;
use LiquidSpace\Request\WorkspaceAvailabilityRequest;
use LiquidSpace\Response\WorkspaceAvailabilityResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WorkspaceAvailabilityResponseTest extends TestCase
{
    public function testGetPeriodsForDate(): void
    {
        $expectedResponseData = [
            [
                'time' => "00:00:00",
                'state' => 0
            ],
            [
                'time' => "00:15:00",
                'state' => 0
            ],
            [
                'time' => "00:30:00",
                'state' => 0
            ],
            [
                'time' => "00:45:00",
                'state' => 0
            ],
            [
                'time' => "01:00:00",
                'state' => 0
            ],
            [
                'time' => "01:15:00",
                'state' => 0
            ],
            [
                'time' => "01:30:00",
                'state' => 0
            ],
            [
                'time' => "01:45:00",
                'state' => 0
            ],
            [
                'time' => "02:00:00",
                'state' => 0
            ],
            [
                'time' => "02:15:00",
                'state' => 0
            ],
            [
                'time' => "02:30:00",
                'state' => 0
            ],
            [
                'time' => "02:45:00",
                'state' => 0
            ],
            [
                'time' => "03:00:00",
                'state' => 0
            ],
            [
                'time' => "03:15:00",
                'state' => 0
            ],
            [
                'time' => "03:30:00",
                'state' => 0
            ],
            [
                'time' => "03:45:00",
                'state' => 0
            ],
            [
                'time' => "04:00:00",
                'state' => 0
            ],
            [
                'time' => "04:15:00",
                'state' => 0
            ],
            [
                'time' => "04:30:00",
                'state' => 0
            ],
            [
                'time' => "04:45:00",
                'state' => 0
            ],
            [
                'time' => "05:00:00",
                'state' => 0
            ],
            [
                'time' => "05:15:00",
                'state' => 0
            ],
            [
                'time' => "05:30:00",
                'state' => 0
            ],
            [
                'time' => "05:45:00",
                'state' => 0
            ],
            [
                'time' => "06:00:00",
                'state' => 0
            ],
            [
                'time' => "06:15:00",
                'state' => 0
            ],
            [
                'time' => "06:30:00",
                'state' => 0
            ],
            [
                'time' => "06:45:00",
                'state' => 0
            ],
            [
                'time' => "07:00:00",
                'state' => 0
            ],
            [
                'time' => "07:15:00",
                'state' => 0
            ],
            [
                'time' => "07:30:00",
                'state' => 0
            ],
            [
                'time' => "07:45:00",
                'state' => 0
            ],
            [
                'time' => "08:00:00",
                'state' => 0
            ],
            [
                'time' => "08:15:00",
                'state' => 0
            ],
            [
                'time' => "08:30:00",
                'state' => 0
            ],
            [
                'time' => "08:45:00",
                'state' => 0
            ],
            [
                'time' => "09:00:00",
                'state' => 2
            ],
            [
                'time' => "09:15:00",
                'state' => 2
            ],
            [
                'time' => "09:30:00",
                'state' => 2
            ],
            [
                'time' => "09:45:00",
                'state' => 2
            ],
            [
                'time' => "10:00:00",
                'state' => 2
            ],
            [
                'time' => "10:15:00",
                'state' => 2
            ],
            [
                'time' => "10:30:00",
                'state' => 2
            ],
            [
                'time' => "10:45:00",
                'state' => 2
            ],
            [
                'time' => "11:00:00",
                'state' => 2
            ],
            [
                'time' => "11:15:00",
                'state' => 2
            ],
            [
                'time' => "11:30:00",
                'state' => 2
            ],
            [
                'time' => "11:45:00",
                'state' => 2
            ],
            [
                'time' => "12:00:00",
                'state' => 2
            ],
            [
                'time' => "12:15:00",
                'state' => 2
            ],
            [
                'time' => "12:30:00",
                'state' => 2
            ],
            [
                'time' => "12:45:00",
                'state' => 2
            ],
            [
                'time' => "13:00:00",
                'state' => 2
            ],
            [
                'time' => "13:15:00",
                'state' => 2
            ],
            [
                'time' => "13:30:00",
                'state' => 2
            ],
            [
                'time' => "13:45:00",
                'state' => 2
            ],
            [
                'time' => "14:00:00",
                'state' => 2
            ],
            [
                'time' => "14:15:00",
                'state' => 2
            ],
            [
                'time' => "14:30:00",
                'state' => 2
            ],
            [
                'time' => "14:45:00",
                'state' => 2
            ],
            [
                'time' => "15:00:00",
                'state' => 2
            ],
            [
                'time' => "15:15:00",
                'state' => 2
            ],
            [
                'time' => "15:30:00",
                'state' => 2
            ],
            [
                'time' => "15:45:00",
                'state' => 2
            ],
            [
                'time' => "16:00:00",
                'state' => 2
            ],
            [
                'time' => "16:15:00",
                'state' => 2
            ],
            [
                'time' => "16:30:00",
                'state' => 2
            ],
            [
                'time' => "16:45:00",
                'state' => 2
            ],
            [
                'time' => "17:00:00",
                'state' => 0
            ],
            [
                'time' => "17:15:00",
                'state' => 0
            ],
            [
                'time' => "17:30:00",
                'state' => 0
            ],
            [
                'time' => "17:45:00",
                'state' => 0
            ],
            [
                'time' => "18:00:00",
                'state' => 0
            ],
            [
                'time' => "18:15:00",
                'state' => 0
            ],
            [
                'time' => "18:30:00",
                'state' => 0
            ],
            [
                'time' => "18:45:00",
                'state' => 0
            ],
            [
                'time' => "19:00:00",
                'state' => 0
            ],
            [
                'time' => "19:15:00",
                'state' => 0
            ],
            [
                'time' => "19:30:00",
                'state' => 0
            ],
            [
                'time' => "19:45:00",
                'state' => 0
            ],
            [
                'time' => "20:00:00",
                'state' => 0
            ],
            [
                'time' => "20:15:00",
                'state' => 0
            ],
            [
                'time' => "20:30:00",
                'state' => 0
            ],
            [
                'time' => "20:45:00",
                'state' => 0
            ],
            [
                'time' => "21:00:00",
                'state' => 0
            ],
            [
                'time' => "21:15:00",
                'state' => 0
            ],
            [
                'time' => "21:30:00",
                'state' => 0
            ],
            [
                'time' => "21:45:00",
                'state' => 0
            ],
            [
                'time' => "22:00:00",
                'state' => 0
            ],
            [
                'time' => "22:15:00",
                'state' => 0
            ],
            [
                'time' => "22:30:00",
                'state' => 0
            ],
            [
                'time' => "22:45:00",
                'state' => 0
            ],
            [
                'time' => "23:00:00",
                'state' => 0
            ],
            [
                'time' => "23:15:00",
                'state' => 0
            ],
            [
                'time' => "23:30:00",
                'state' => 0
            ],
            [
                'time' => "23:45:00",
                'state' => 0
            ]
        ];

        // @phpstan-ignore-next-line
        $mockResponse = new JsonMockResponse($expectedResponseData, [
            'http_code' => 200,
            'response_headers' => ['content-type' => 'application/json; charset=utf-8']
        ]);

        $client = $this->createClient(new MockHttpClient([$mockResponse]));

        $request = new WorkspaceAvailabilityRequest(
            'f0c0de3d-6b54-43b5-af43-000929469a4d',
            'e4b94f1d-da6c-40a0-9b2a-47ac61fd0671',
            new \DateTimeImmutable('2023-06-21T00:00:00'),
            AvailabilityGranularity::FifteenMinutes
        );

        $actualResponse = $client->request($request, WorkspaceAvailabilityResponse::class);

        $date = new \DateTimeImmutable('2023-06-21T00:00:00', new \DateTimeZone('America/New_York'));
        $localPeriods = $actualResponse?->getPeriodsForDate($date);

        self::assertNotNull($localPeriods);
        self::assertEquals(new WorkspaceLocalAvailabilityPeriod([
            'state' => 0,
            'datetime' => new \DateTimeImmutable(
                '2023-06-21T00:00:00',
                new \DateTimeZone('America/New_York')
            )
        ]), $localPeriods[0]);
        self::assertEquals(new WorkspaceLocalAvailabilityPeriod([
            'state' => 0,
            'datetime' => new \DateTimeImmutable(
                '2023-06-21T23:45:00',
                new \DateTimeZone('America/New_York')
            )
        ]), $localPeriods[95]);
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