<?php

namespace Client\Tests\Client;

use Client\LiquidSpaceClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class LiquidSpaceClientTest extends TestCase
{
    public function testApiKeyAddedToHeaders(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);

        $mockHttpClient
            ->expects(self::once())
            ->method('withOptions')
            ->with([
                'headers' => [
                    'LS-Subscription-Key' => 'test',
                ],
                'http_version' => '2.0',
                'base_uri' => 'https://ls-api-dev.azure-api.net',
            ])
        ;

        $this->createClient($mockHttpClient);
    }

    private function createClient(?HttpClientInterface $httpClient = null): LiquidSpaceClient
    {
        return new LiquidSpaceClient(
            $httpClient ?? new MockHttpClient(),
            'test'
        );
    }
}