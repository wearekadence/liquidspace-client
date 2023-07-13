<?php

namespace LiquidSpace\Tests\Client;

use LiquidSpace\Client;
use LiquidSpace\Entity\Impersonation;
use LiquidSpace\Exception\MemberNotFoundException;
use LiquidSpace\Exception\UnableToImpersonateException;
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

    public static function getEnterpriseTokenDataProvider(): array
    {
        $cache = new ArrayAdapter();
        $cache->get('liquidspace|enterprise|token|clientId', fn () => 'abcd');

        // @phpstan-ignore-next-line
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

        // @phpstan-ignore-next-line
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
                'expectedExceptionClass' => MemberNotFoundException::class,
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
     * @psalm-param class-string<Throwable>|null $expectedExceptionClass
     */
    public function testGetMemberId(
        HttpClientInterface $client,
        CacheInterface $cache,
        ?string $expectedExceptionClass,
        ?string $expectedId
    ): void {
        $token = null;

        // Did not use expectException() because we need to test that the cache is emptied when the token is expired
        try {
            $token = $this->createClient($client, $cache)->getMemberId(
                'accountId',
                'john.smith@example.com',
                'enterpriseToken'
            );

            if (null !== $expectedExceptionClass) {
                self::fail('Expected exception, got none');
            }
        } catch (Throwable $e) {
            if ($e::class !== $expectedExceptionClass) {
                self::fail(sprintf('Expected exception %s, got %s', $expectedExceptionClass, $e::class));
            }
        }

        $cachedMemberId = $cache->get('liquidspace|member|id|am9obi5zbWl0aEBleGFtcGxlLmNvbQ==', fn () => null);

        self::assertEquals($expectedId, $token);
        self::assertEquals($expectedId, $cachedMemberId);
    }

    public static function getMemberTokenDataProvider(): array
    {
        $cache = new ArrayAdapter();
        $cache->get('liquidspace|member|token|fb92ee34-af5b-4abf-8d02-155231d13fdd', fn () => 'abcd');

        // @phpstan-ignore-next-line
        $foundResponse = new JsonMockResponse([
            'access_token' => 'Y2xpZW50U2VjcmV0',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'lsapi.marketplace',
            'issued_token_type' => 'urn:ietf:params:oauth:token-type:access_token'
        ]);

        $expiredResponse = new JsonMockResponse([], [
            'http_code' => 400,
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
            'with empty cache, expired enterprise token' => [
                'client' => new MockHttpClient([$expiredResponse]),
                'cache' => new ArrayAdapter(),
                'expectedExceptionClass' => UnauthorizedException::class,
                'expectedId' => null,
            ],
        ];
    }

    #[DataProvider('getMemberTokenDataProvider')]
    /**
     * @psalm-param class-string<Throwable>|null $expectedExceptionClass
     */
    public function testGetMemberToken(
        HttpClientInterface $client,
        CacheInterface $cache,
        ?string $expectedExceptionClass,
        ?string $expectedId
    ): void {
        $token = null;

        // Did not use expectException() because we need to test that the cache is emptied when the token is expired
        try {
            $token = $this->createClient($client, $cache)->getMemberToken(
                'fb92ee34-af5b-4abf-8d02-155231d13fdd',
                'enterpriseToken'
            );

            if (null !== $expectedExceptionClass) {
                self::fail('Expected exception, got none');
            }
        } catch (Throwable $e) {
            if ($e::class !== $expectedExceptionClass) {
                self::fail(sprintf('Expected exception %s, got %s', $expectedExceptionClass, $e::class));
            }
        }

        $cachedToken = $cache->get('liquidspace|member|token|fb92ee34-af5b-4abf-8d02-155231d13fdd', fn () => null);

        self::assertEquals($expectedId, $token);
        self::assertEquals($expectedId, $cachedToken);
    }

    public static function impersonateDataProvider(): array
    {
        $cache = new ArrayAdapter();
        $cache->get('liquidspace|enterprise|token|clientId', fn () => 'cachedEnterpriseToken');
        $cache->get('liquidspace|member|id|am9obi5zbWl0aEBleGFtcGxlLmNvbQ==', fn () => '624d234f-b429-40e2-a964-c021baf6594f');
        $cache->get('liquidspace|team|id|am9obi5zbWl0aEBleGFtcGxlLmNvbQ==', fn () => '0af32b78-09ca-4d08-b7a1-f5ba83614375');
        $cache->get('liquidspace|team|prepay|0af32b78-09ca-4d08-b7a1-f5ba83614375', fn () => true);
        $cache->get('liquidspace|member|token|624d234f-b429-40e2-a964-c021baf6594f', fn () => 'cachedMemberToken');

        // @phpstan-ignore-next-line
        $goodEnterpriseTokenResponse = new JsonMockResponse([
            'access_token' => 'enterpriseToken',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'lsapi.full'
        ]);

        $goodMemberIdResponse = new JsonMockResponse([
            'id' => '2bb4c700-fe89-42d3-9a4b-1f6d8586d6b7',
            'fullName' => 'John Smith',
            'email' => 'john.smith@example.com',
            'teamId' => '0af32b78-09ca-4d08-b7a1-f5ba83614375'
            // ...
        ]);

        // @phpstan-ignore-next-line
        $goodMemberTokenResponse = new JsonMockResponse([
            'access_token' => 'memberToken',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'lsapi.marketplace',
            'issued_token_type' => 'urn:ietf:params:oauth:token-type:access_token'
        ]);

        // @phpstan-ignore-next-line
        $goodTeamResponse = new JsonMockResponse([
            'id' => '0af32b78-09ca-4d08-b7a1-f5ba83614375',
            'headquarters' => [],
            'paymentMethodList' => [
                'paymentMethods' => [
                    [
                        'paymentProfileId' => 'ce08e5fc',
                        'customerProfileId' => '0af32b78-09ca-4d08-b7a1-f5ba83614375',
                        'paymentProvider' => 1,
                        'creditCardType' => 0,
                        'name' => 'Visa',
                        'maskedNumber' => '*1111',
                        'expirationMonth' => 7,
                        'expirationYear' => 2024,
                        'isExpired' => false,
                        'isDefault' => true,
                        'description' => 'BT Dev Test CC',
                        'isVerified' => false,
                        'enabled' => true
                    ]
                ],
                'canDeleteBankAccount' => true,
                'enableACHPayments' => true,
                'ccEntryDisabled' => false
            ],
            'enterpriseAccountId' => '8e38ca77-fac0-4896-b9b6-ff7dd5dc71f6',
            'ownerEmail' => 'eric@liquidspace.com',
            'name' => 'Kadence (Demo) team',
            'notificationEmails' => [],
            'isActive' => true,
            'adminEmails' => [
                'jordan@kadence.co',
                'jamie@kadence.co',
                'caitlyn@kadence.co',
                'michael.bole@kadence.co'
            ],
            'maxDailyBudget' => null,
            'maxMonthlyBudget' => null,
            'maxMonthlyMemberBudget' => null,
            'disableCCEntry' => false,
            'searchSettings' => [
                'PreferredVenue' => false,
                'PreferredVenueImmutableOnSRP' => false,
                'Extended' => false
            ],
            'extendedSearchOptions' => [
                'reservationMethod' => null,
                'spaceTypes' => 0,
                'minPrice' => null,
                'maxPrice' => null,
                'liquidSpacePro' => false,
                'immutableOptions' => [],
                'hourlySpaceTypesWithoutTrainingArray' => [
                    1,
                    2,
                    4,
                    64
                ],
                'monthlySpaceTypesWithoutDesksArray' => [
                    4,
                    16,
                    32
                ]
            ],
            'bannerOptions' => [
                'title' => null,
                'subtitle' => null,
                'popupTitle' => null,
                'popupContent' => null,
                'enable' => false,
                'forcePopup' => false
            ]
        ]);

        $expiredResponse = new JsonMockResponse([], [
            'http_code' => 401,
        ]);

        return [
            'with valid cache' => [
                'client' => new MockHttpClient(),
                'cache' => $cache,
                'expectedImpersonation' => new Impersonation(
                    '12dd7de1-cacf-4419-ac5d-fe31084f2482',
                    '624d234f-b429-40e2-a964-c021baf6594f',
                    'cachedEnterpriseToken',
                    'cachedMemberToken'
                ),
            ],
            'with empty cache' => [
                'client' => new MockHttpClient([
                    $goodEnterpriseTokenResponse,
                    $goodMemberIdResponse,
                    $goodMemberIdResponse,
                    $goodTeamResponse,
                    $goodMemberTokenResponse,
                ]),
                'cache' => new ArrayAdapter(),
                'expectedImpersonation' => new Impersonation(
                    '12dd7de1-cacf-4419-ac5d-fe31084f2482',
                    '2bb4c700-fe89-42d3-9a4b-1f6d8586d6b7',
                    'enterpriseToken',
                    'memberToken'
                ),
            ],
            'with empty cache, expired enterprise token' => [
                'client' => new MockHttpClient([
                    $goodEnterpriseTokenResponse,
                    $expiredResponse,
                    $goodEnterpriseTokenResponse,
                    $goodMemberIdResponse,
                    $goodMemberIdResponse,
                    $goodTeamResponse,
                    $goodMemberTokenResponse,
                ]),
                'cache' => new ArrayAdapter(),
                'expectedImpersonation' => new Impersonation(
                    '12dd7de1-cacf-4419-ac5d-fe31084f2482',
                    '2bb4c700-fe89-42d3-9a4b-1f6d8586d6b7',
                    'enterpriseToken',
                    'memberToken'
                ),
            ],
            'with empty cache, multiple failed retries' => [
                'client' => new MockHttpClient([
                    $goodEnterpriseTokenResponse,
                    $expiredResponse,
                    $goodEnterpriseTokenResponse,
                    $expiredResponse,
                    $goodEnterpriseTokenResponse,
                    $expiredResponse,
                ]),
                'cache' => new ArrayAdapter(),
                'expectedImpersonation' => null,
            ],
        ];
    }

    #[DataProvider('impersonateDataProvider')]
    public function testImpersonate(
        HttpClientInterface $client,
        CacheInterface $cache,
        ?Impersonation $expectedImpersonation
    ): void {
        if (null === $expectedImpersonation) {
            self::expectException(UnableToImpersonateException::class);
        }

        $actualImpersonation = $this->createClient($client, $cache)->impersonate(
            '12dd7de1-cacf-4419-ac5d-fe31084f2482',
            'john.smith@example.com'
        );

        self::assertEquals($expectedImpersonation, $actualImpersonation);
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