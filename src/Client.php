<?php

namespace LiquidSpace;

use LiquidSpace\Entity\Impersonation;
use LiquidSpace\Request\HttpMethod;
use LiquidSpace\Request\RequestInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private const BASE_URI = 'https://ls-api-dev.azure-api.net';

    private string $subscriptionKey;
    private string $clientId;
    private string $clientSecret;

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
            if (404 === $e->getCode()) {
                return null;
            }

            throw $e;
        }
    }

    public function impersonate(
        string $accountId,
        string $email,
    ): Impersonation {
        // Step 1: Get Client Credentials OAuth2 Token
        $enterpriseAccessToken = $this->cache->get('liquidspace:enterprise:token:'.$this->clientId, function (ItemInterface $item) {
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

        // Step 2: Lookup member ID from email
        $memberId = $this->cache->get(
            'liquidspace:member:id:'.\base64_encode($email),
            function () use ($accountId, $email, $enterpriseAccessToken) {
                $memberResponse = $this->httpClient->request(
                    HttpMethod::Post->value,
                    '/enterpriseaccountmanagement/api/enterpriseaccounts/'.$accountId.'/members/'.$email,
                    [
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer '.$enterpriseAccessToken,
                        ],
                        'body' => [
                            'grant_type' => 'client_credentials',
                            'scope' => 'lsapi.full',
                        ],
                    ]
                );

                $memberData = $memberResponse->toArray();

                return $memberData['id'];
            }
        );

        // Step 3: Get Member Access Token
        $memberAccessToken = $this->cache->get(
            'liquidspace:member:token:'.$memberId,
            function (ItemInterface $item) use ($memberId, $enterpriseAccessToken) {
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
                        'subject_token' => $enterpriseAccessToken,
                        'subject_token_type' => 'urn:ietf:params:oauth:token-type:access_token',
                        'scope' => 'lsapi.marketplace',
                    ],
                ]);

                $memberTokenData = $memberTokenResponse->toArray();

                return $memberTokenData['access_token'];
            }
        );

        return new Impersonation($accountId, $memberId, $enterpriseAccessToken, $memberAccessToken);
    }

    public function getEnterpriseAuthorization(): string
    {
        return \base64_encode($this->clientId.':'.$this->clientSecret);
    }
}
