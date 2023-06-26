<?php

namespace LiquidSpace;

use LiquidSpace\Entity\Impersonation;
use LiquidSpace\Exception\EnterpriseTokenFetchFailedException;
use LiquidSpace\Exception\MemberFetchFailedException;
use LiquidSpace\Exception\MemberNotFoundException;
use LiquidSpace\Exception\MemberRegistrationFailedException;
use LiquidSpace\Exception\MemberTokenFetchFailedException;
use LiquidSpace\Exception\NoPaymentMethodOnAccountException;
use LiquidSpace\Exception\TeamFetchFailedException;
use LiquidSpace\Exception\TeamNotFoundException;
use LiquidSpace\Exception\UnableToImpersonateException;
use LiquidSpace\Exception\UnauthorizedException;
use LiquidSpace\Request\HttpMethod;
use LiquidSpace\Request\RequestInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private const BASE_URI = 'https://ls-api-dev.azure-api.net';
    private const MAX_IMPERSONATION_RETRY_COUNT = 3;

    private string $subscriptionKey;
    private string $clientId;
    private string $clientSecret;
    private int $impersonationRetryCount = 0;

    public function __construct(
        protected HttpClientInterface $httpClient,
        protected CacheInterface $cache,
        string $subscriptionKey,
        string $clientId,
        string $clientSecret,
    ) {
        $this->subscriptionKey = $subscriptionKey;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->httpClient = $this->httpClient->withOptions([
            'headers' => [
                'LS-Subscription-Key' => $this->subscriptionKey,
            ],
            'http_version' => '2.0',
            'base_uri' => self::BASE_URI,
        ]);
    }

    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $responseClass
     *
     * @return T|null
     *
     * @throws TransportExceptionInterface
     */
    public function request(
        RequestInterface $request,
        string $responseClass,
        Impersonation $impersonation = null
    ): ?object {
        $options = $request->getOptions();
        if ($request->requiresEnterpriseToken()) {
            $enterpriseToken = $this->getEnterpriseToken();
            $options['headers']['Authorization'] = 'Bearer '.$enterpriseToken;
        }

        $response = $this->httpClient->request(
            $request->getMethod()->value,
            $request->getPath(),
            $options
        );

        try {
            return new $responseClass($response);
        } catch (ClientException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getCode()) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * @throws EnterpriseTokenFetchFailedException
     * @throws MemberFetchFailedException
     * @throws MemberNotFoundException
     * @throws MemberRegistrationFailedException
     * @throws MemberTokenFetchFailedException
     * @throws NoPaymentMethodOnAccountException
     * @throws TeamFetchFailedException
     * @throws TeamNotFoundException
     * @throws UnableToImpersonateException
     * @throws InvalidArgumentException
     */
    public function impersonate(
        string $accountId,
        string $memberEmail,
    ): Impersonation {
        $this->impersonationRetryCount = 1;

        $impersonation = null;
        $lastUnauthorizedException = null;

        while (null === $impersonation && $this->impersonationRetryCount <= self::MAX_IMPERSONATION_RETRY_COUNT) {
            try {
                $impersonation = $this->tryImpersonation($accountId, $memberEmail);
            } catch (UnauthorizedException $exception) {
                $lastUnauthorizedException =  $exception;
                ++$this->impersonationRetryCount;
            }
        }

        if (null === $impersonation) {
            throw new UnableToImpersonateException(
                'Unable to impersonate: '.$memberEmail,
                previous: $lastUnauthorizedException
            );
        }

        return $impersonation;
    }

    /**
     * @param string $accountId LiquidSpace account ID
     * @param string $email     Member's email address
     * @param string $fullName  Member's full name
     *
     * @return string Member ID
     *
     * @throws UnauthorizedException
     * @throws MemberRegistrationFailedException
     * @throws MemberNotFoundException
     * @throws MemberFetchFailedException
     * @throws EnterpriseTokenFetchFailedException
     */
    public function createMember(string $accountId, string $email, string $fullName): string
    {
        // Step 1: Get Client Credentials Token (Enterprise Token)
        $enterpriseToken = $this->getEnterpriseToken();

        // Step 2: Register Member
        $this->registerMember($accountId, $email, $fullName, $enterpriseToken);

        // Step 3: Lookup member ID from email address
        return $this->getMemberId($accountId, $email, $enterpriseToken);
    }

    public function getEnterpriseAuthorization(): string
    {
        return \base64_encode($this->clientId.':'.$this->clientSecret);
    }

    /**
     * @throws EnterpriseTokenFetchFailedException
     * @throws MemberFetchFailedException
     * @throws MemberRegistrationFailedException
     * @throws MemberNotFoundException
     * @throws UnauthorizedException
     * @throws TeamNotFoundException
     * @throws TeamFetchFailedException
     * @throws NoPaymentMethodOnAccountException
     * @throws MemberTokenFetchFailedException
     * @throws InvalidArgumentException
     */
    public function tryImpersonation(string $accountId, string $memberEmail): Impersonation
    {
        // Step 1: Get Client Credentials Token (Enterprise Token)
        $enterpriseToken = $this->getEnterpriseToken();

        // Step 2: Lookup member ID from email address
        try {
            $memberId = $this->getMemberId($accountId, $memberEmail, $enterpriseToken);
        } catch (MemberNotFoundException) {
            // Step 2a: If member not found, create member
            $memberId = $this->createMember($accountId, $memberEmail, $memberEmail);
        }

        // Step 4: Lookup team ID from email address
        $teamId = $this->getTeamId($accountId, $memberEmail, $enterpriseToken);

        // Step 5: Check if member has access to pre-pay via team membership
        $prePayEnabled = $this->getPrePayEnabled($teamId, $enterpriseToken);

        if (false === $prePayEnabled) {
            throw new NoPaymentMethodOnAccountException('No payment method available to member: '.$memberId);
        }

        // Step 3: Get Access Token (Member Token)
        $memberToken = $this->getMemberToken($memberId, $enterpriseToken);

        return new Impersonation($accountId, $memberId, $enterpriseToken, $memberToken);
    }

    /**
     * @throws EnterpriseTokenFetchFailedException
     * @throws InvalidArgumentException
     */
    public function getEnterpriseToken(): string
    {
        return $this->cache->get('liquidspace|enterprise|token|'.$this->clientId, function (ItemInterface $item) {
            $item->expiresAfter(3600 - 10);

            $clientCredentialsResponse = $this->httpClient->request(HttpMethod::Post->value, '/identity/connect/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '.$this->getEnterpriseAuthorization(),
                ],
                'body' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'lsapi.full',
                ],
            ]);

            try {
                $clientCredentialsData = $clientCredentialsResponse->toArray();
            } catch (\Exception $exception) {
                throw new EnterpriseTokenFetchFailedException(
                    'Unable to get enterprise token for client: '.$this->clientId,
                    previous: $exception
                );
            }

            return $clientCredentialsData['access_token'];
        });
    }

    /**
     * @throws UnauthorizedException
     * @throws MemberNotFoundException
     * @throws MemberFetchFailedException
     * @throws InvalidArgumentException
     */
    public function getMemberId(string $accountId, string $memberEmail, string $enterpriseToken): string
    {
        return $this->cache->get(
            'liquidspace|member|id|'.\base64_encode($memberEmail),
            function () use ($accountId, $memberEmail, $enterpriseToken) {
                $memberResponse = $this->httpClient->request(
                    HttpMethod::Get->value,
                    '/enterpriseaccountmanagement/api/enterpriseaccounts/'.$accountId.'/members/'.$memberEmail,
                    [
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer '.$enterpriseToken,
                        ],
                    ]
                );

                try {
                    $memberData = $memberResponse->toArray();
                } catch (ClientException $exception) {
                    if (Response::HTTP_UNAUTHORIZED === $exception->getCode()) {
                        $this->cache->delete('liquidspace|enterprise|token|'.$this->clientId);
                        throw new UnauthorizedException($exception->getMessage(), previous: $exception);
                    } elseif (Response::HTTP_NOT_FOUND === $exception->getCode()) {
                        throw new MemberNotFoundException(
                            'Member not found for: '.$memberEmail,
                            previous: $exception
                        );
                    } else {
                        throw new MemberFetchFailedException(
                            'Failed to fetch member: '.$memberEmail,
                            previous: $exception
                        );
                    }
                } catch (\Exception $exception) {
                    throw new MemberFetchFailedException(
                        'Failed to fetch member: '.$memberEmail,
                        previous: $exception
                    );
                }

                return $memberData['id'];
            }
        );
    }

    /**
     * @throws UnauthorizedException
     * @throws MemberNotFoundException
     * @throws MemberFetchFailedException
     * @throws InvalidArgumentException
     */
    public function getTeamId(string $accountId, string $memberEmail, string $enterpriseToken): string
    {
        return $this->cache->get(
            'liquidspace|team|id|'.\base64_encode($memberEmail),
            function (ItemInterface $item) use ($accountId, $memberEmail, $enterpriseToken) {
                $item->expiresAfter(14400); // 4 hours

                $memberResponse = $this->httpClient->request(
                    HttpMethod::Get->value,
                    '/enterpriseaccountmanagement/api/enterpriseaccounts/'.$accountId.'/members/'.$memberEmail,
                    [
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer '.$enterpriseToken,
                        ],
                    ]
                );

                try {
                    $memberData = $memberResponse->toArray();
                } catch (ClientException $exception) {
                    if (Response::HTTP_UNAUTHORIZED === $exception->getCode()) {
                        $this->cache->delete('liquidspace|enterprise|token|'.$this->clientId);
                        throw new UnauthorizedException($exception->getMessage(), previous: $exception);
                    } elseif (Response::HTTP_NOT_FOUND === $exception->getCode()) {
                        throw new MemberNotFoundException(
                            'Member not found for: '.$memberEmail,
                            previous: $exception
                        );
                    } else {
                        throw new MemberFetchFailedException(
                            'Failed to fetch member: '.$memberEmail,
                            previous: $exception
                        );
                    }
                } catch (\Exception $exception) {
                    throw new MemberFetchFailedException(
                        'Failed to fetch member: '.$memberEmail,
                        previous: $exception
                    );
                }

                return $memberData['teamId'];
            }
        );
    }

    /**
     * @throws UnauthorizedException
     * @throws MemberTokenFetchFailedException
     * @throws InvalidArgumentException
     */
    public function getMemberToken(string $memberId, string $enterpriseToken): string
    {
        return $this->cache->get(
            'liquidspace|member|token|'.$memberId,
            function (ItemInterface $item) use ($memberId, $enterpriseToken) {
                $item->expiresAfter(3600 - 10);

                $memberTokenResponse = $this->httpClient->request(HttpMethod::Post->value, '/identity/connect/token', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Authorization' => 'Basic '.$this->getEnterpriseAuthorization(),
                    ],
                    'body' => [
                        'exchange_style' => 'impersonation',
                        'act_as' => $memberId,
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:token-exchange',
                        'subject_token' => $enterpriseToken,
                        'subject_token_type' => 'urn:ietf:params:oauth:token-type:access_token',
                        'scope' => 'lsapi.marketplace',
                    ],
                ]);

                try {
                    $memberTokenData = $memberTokenResponse->toArray();
                } catch (ClientException $exception) {
                    if (Response::HTTP_BAD_REQUEST === $exception->getCode()) {
                        $this->cache->delete('liquidspace|enterprise|token|'.$this->clientId);
                        throw new UnauthorizedException($exception->getMessage(), previous: $exception);
                    } else {
                        throw new MemberTokenFetchFailedException(
                            'Failed to fetch token for member id: '.$memberId,
                            previous: $exception
                        );
                    }
                } catch (\Exception $exception) {
                    throw new MemberTokenFetchFailedException(
                        'Failed to fetch token for member id: '.$memberId,
                        previous: $exception
                    );
                }

                return $memberTokenData['access_token'];
            }
        );
    }

    /**
     * @throws UnauthorizedException
     * @throws TeamNotFoundException
     * @throws TeamFetchFailedException
     * @throws InvalidArgumentException
     */
    public function getPrePayEnabled(string $teamId, string $enterpriseToken): bool
    {
        return $this->cache->get(
            'liquidspace|team|prepay|'.$teamId,
            function (ItemInterface $item) use ($teamId, $enterpriseToken) {
                $item->expiresAfter(14400); // 4 hours

                $memberResponse = $this->httpClient->request(
                    HttpMethod::Get->value,
                    '/enterpriseaccountmanagement/api/teams/'.$teamId,
                    [
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer '.$enterpriseToken,
                        ],
                    ]
                );

                try {
                    $teamData = $memberResponse->toArray();
                } catch (ClientException $exception) {
                    if (Response::HTTP_UNAUTHORIZED === $exception->getCode()) {
                        $this->cache->delete('liquidspace|enterprise|token|'.$this->clientId);
                        throw new UnauthorizedException($exception->getMessage(), previous: $exception);
                    } elseif (Response::HTTP_NOT_FOUND === $exception->getCode()) {
                        throw new TeamNotFoundException('Team not found for: '.$teamId, previous: $exception);
                    } else {
                        throw new TeamFetchFailedException(
                            'Failed to fetch team: '. $teamId,
                            previous: $exception
                        );
                    }
                } catch (\Exception $exception) {
                    throw new TeamFetchFailedException(
                        'Failed to fetch team: '. $teamId,
                        previous: $exception
                    );
                }

                $paymentMethods = $teamData['paymentMethodList']['paymentMethods'];

                foreach ($paymentMethods as $paymentMethod) {
                    if (true === $paymentMethod['enabled'] && false === $paymentMethod['isExpired']) {
                        return true;
                    }
                }

                return false;
            }
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws MemberRegistrationFailedException
     */
    public function registerMember(
        string $accountId,
        string $email,
        string $fullName,
        string $enterpriseToken
    ): void {
        $createResponse = $this->httpClient->request(
            HttpMethod::Post->value,
            '/enterpriseaccountmanagement/api/enterpriseaccounts/'.$accountId.'/members',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$enterpriseToken,
                ],
                'body' => [
                    'email' => $email,
                    'fullName' => $fullName,
                ],
            ]
        );

        try {
            $createData = $createResponse->toArray();
        } catch (ClientException $exception) {
            if (Response::HTTP_BAD_REQUEST === $exception->getCode()) {
                $this->cache->delete('liquidspace|enterprise|token|'.$this->clientId);
                throw new UnauthorizedException($exception->getMessage(), previous: $exception);
            } else {
                throw new MemberRegistrationFailedException('Member registration failed', previous: $exception);
            }
        } catch (\Exception $exception) {
            throw new MemberRegistrationFailedException('Member registration failed', previous: $exception);
        }
    }
}
