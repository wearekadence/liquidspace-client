<?php

namespace LiquidSpace\Tests\Client;

use LiquidSpace\Client;
use LiquidSpace\Exception\MemberNotFound;
use LiquidSpace\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

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
                    'LS-Subscription-Key' => 'subscriptionKey',
                ],
                'http_version' => '2.0',
                'base_uri' => 'https://ls-api-dev.azure-api.net',
            ])
        ;

        $this->createClient($mockHttpClient);
    }

    public function testConstructEnterpriseAuthorizationHeader(): void
    {
        $header = $this->createClient()->getEnterpriseAuthorization();

        self::assertEquals('Y2xpZW50SWQ6Y2xpZW50U2VjcmV0', $header);
    }

    public static function getEnterpriseTokenDataProvider(): array
    {
        $cache = new ArrayAdapter();
        $cache->get('liquidspace|enterprise|token|clientId', fn () => 'abcd');

        $mockResponse = new JsonMockResponse([
            'access_token' => 'Y2xpZW50U2VjcmV0',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'lsapi.full'
        ]);

        return [
            'with valid cache' => [
                'client' => new MockHttpClient(),
                'cache' => $cache,
                'expectedToken' => 'abcd',
            ],
            'with empty cache' => [
                'client' => new MockHttpClient([$mockResponse]),
                'cache' => new ArrayAdapter(),
                'expectedToken' => 'Y2xpZW50U2VjcmV0',
            ],
        ];
    }

    #[DataProvider('getEnterpriseTokenDataProvider')]
    public function testGetEnterpriseToken(
        HttpClientInterface $client,
        CacheInterface $cache,
        string $expectedToken
    ): void {
        $token = $this->createClient($client, $cache)->getEnterpriseToken();

        self::assertEquals($expectedToken, $token);
        self::assertEquals($expectedToken, $cache->get('liquidspace|enterprise|token|clientId', fn () => null));
    }

    public static function getMemberIdDataProvider(): array
    {
        $cache = new ArrayAdapter();
        $cache->get('liquidspace|member|id|am9obi5zbWl0aEBleGFtcGxlLmNvbQ==', fn () => 'abcd');

        $foundResponse = new JsonMockResponse([
            'id' => 'Y2xpZW50U2VjcmV0',
            'fullName' => 'John Smith',
            'email' => 'john.smith@example.com'
            // ...
        ]);

        $notFoundResponse = new JsonMockResponse([
            'type' => 'https://tools.ietf.org/html/rfc7231#section-6.5.4',
            'title' => 'Not Found',
            'status' => 404,
            'traceId' => '00-f8e56aed99ba4104a1f0c8e85ab7f3c2-62d7a8157cd36134-00'
        ], [
            'http_code' => 404,
        ]);

        $expiredResponse = new JsonMockResponse([], [
            'http_code' => 401,
        ]);

        return [
            'with valid cache' => [
                'client' => new MockHttpClient(),
                'cache' => $cache,
                'expectedExceptionClass' => null,
                'expectedId' => 'abcd',
            ],
            'with empty cache, member found' => [
                'client' => new MockHttpClient([$foundResponse]),
                'cache' => new ArrayAdapter(),
                'expectedExceptionClass' => null,
                'expectedId' => 'Y2xpZW50U2VjcmV0',
            ],
            'with empty cache, member not found' => [
                'client' => new MockHttpClient([$notFoundResponse]),
                'cache' => new ArrayAdapter(),
                'expectedExceptionClass' => MemberNotFound::class,
                'expectedId' => null,
            ],
            'with empty cache, expired enterprise token' => [
                'client' => new MockHttpClient([$expiredResponse]),
                'cache' => new ArrayAdapter(),
                'expectedExceptionClass' => UnauthorizedException::class,
                'expectedId' => null,
            ],
        ];
    }

    #[DataProvider('getMemberIdDataProvider')]
    /**
     * @psalm-param class-string<Throwable> $expectedExceptionClass
     */
    public function testGetMemberId(
        HttpClientInterface $client,
        CacheInterface $cache,
        ?string $expectedExceptionClass,
        ?string $expectedId
    ): void {
        if (null !== $expectedExceptionClass) {
            self::expectException($expectedExceptionClass);
        }

        $token = $this->createClient($client, $cache)->getMemberId(
            'accountId',
            'john.smith@example.com',
            'enterpriseToken'
        );

        self::assertEquals($expectedId, $token);
        self::assertEquals($expectedId, $cache->get('liquidspace|member|id|am9obi5zbWl0aEBleGFtcGxlLmNvbQ==', fn () => null));
    }

    private function createClient(?HttpClientInterface $httpClient = null, ?CacheInterface $cache = null): Client
    {
        return new Client(
            $httpClient ?? new MockHttpClient(),
                $cache ?? new ArrayAdapter(),
            'subscriptionKey',
            'clientId',
            'clientSecret',
        );
    }
}