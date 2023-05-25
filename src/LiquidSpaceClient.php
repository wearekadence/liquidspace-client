<?php

namespace Client;

use Client\Request\RequestInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LiquidSpaceClient
{
    private const BASE_URI = 'https://ls-api-dev.azure-api.net';

    private string $apiKey;

    public function __construct(
        protected HttpClientInterface $httpClient,
        string $liquidSpaceApiKey
    ) {
        $this->apiKey = $liquidSpaceApiKey;
        $this->httpClient = $this->httpClient->withOptions([
            'headers' => [
                'LS-Subscription-Key' => $this->apiKey,
            ],
            'http_version' => '2.0',
            'base_uri' => self::BASE_URI,
        ]);
    }

    /**
     * @template T of object
     * @psalm-param class-string<T> $responseClass
     * @return T
     *
     * @throws TransportExceptionInterface
     */
    public function request(RequestInterface $request, string $responseClass): object
    {
        $response = $this->httpClient->request(
            $request->getMethod()->value,
            $request->getPath(),
            $request->getOptions()
        );

        return new $responseClass($response);
    }
}
