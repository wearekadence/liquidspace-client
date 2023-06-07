<?php

namespace LiquidSpace;

use LiquidSpace\Entity\Impersonation;
use LiquidSpace\Exception\UnableToImpersonate;
use LiquidSpace\Exception\UnauthorizedException;
use LiquidSpace\Request\HttpMethod;
use LiquidSpace\Request\RequestInterface;
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
        $response = $this->httpClient->request(
            $request->getMethod()->value,
            $request->getPath(),
            $request->getOptions()
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

    public function impersonate(
        string $accountId,
        string $memberEmail,
    ): Impersonation {
        $this->impersonationRetryCount = 0;

        $impersonation = null;

        do {
            try {
                $impersonation = $this->tryImpersonation($accountId, $memberEmail);
            } catch (UnauthorizedException $e) {
                ++$this->impersonationRetryCount;
            }
        } while (null === $impersonation && $this->impersonationRetryCount <= self::MAX_IMPERSONATION_RETRY_COUNT);

        if (null === $impersonation) {
            throw new UnableToImpersonate('Unable to impersonate: '.$memberEmail);
        }

        return $impersonation;
    }

    protected function getEnterpriseAuthorization(): string
    {
        return \base64_encode($this->clientId.':'.$this->clientSecret);
    }

    /**
     * @throws UnauthorizedException
     */
    protected function tryImpersonation(string $accountId, string $memberEmail): Impersonation
    {
        // Step 1: Get Client Credentials Token (Enterprise Token)
        $enterpriseToken = $this->getEnterpriseToken();

        // Step 2: Lookup member ID from email address
        $memberId = $this->getMemberId($accountId, $memberEmail, $enterpriseToken);

        // Step 3: Get Access Token (Member Token)
        $memberToken = $this->getMemberToken($memberEmail, $enterpriseToken);

        return new Impersonation($accountId, $memberId, $enterpriseToken, $memberToken);
    }

    protected function getEnterpriseToken(): string
    {
        return $this->cache->get('liquidspace:enterprise:token:'.$this->clientId, function (ItemInterface $item) {
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

            $clientCredentialsData = $clientCredentialsResponse->toArray();

            return $clientCredentialsData['access_token'];
        });
    }

    protected function getMemberId(string $accountId, string $memberEmail, string $enterpriseToken): string
    {
        return $this->cache->get(
            'liquidspace:member:id:'.\base64_encode($memberEmail),
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
                        $this->cache->delete('liquidspace:enterprise:token:'.$this->clientId);
                        throw new UnauthorizedException($exception->getMessage(), previous: $exception);
                    } else {
                        throw $exception;
                    }
                }

                return $memberData['id'];
            }
        );
    }

    protected function getMemberToken(string $memberId, string $enterpriseToken): string
    {
        return $this->cache->get(
            'liquidspace:member:token:'.$memberId,
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
                        $this->cache->delete('liquidspace:enterprise:token:'.$this->clientId);
                        throw new UnauthorizedException($exception->getMessage(), previous: $exception);
                    } else {
                        throw $exception;
                    }
                }

                return $memberTokenData['access_token'];
            }
        );
    }
}
