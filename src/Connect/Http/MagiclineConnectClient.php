<?php

namespace AlexBabintsev\Magicline\Connect\Http;

use AlexBabintsev\Magicline\Connect\Exceptions\ConnectApiException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class MagiclineConnectClient
{
    private PendingRequest $httpClient;

    public function __construct(
        private readonly Factory $httpFactory,
        private readonly string $baseUrl,
        private readonly int $timeout = 30,
        private readonly array $retryConfig = ['times' => 3, 'sleep' => 100],
        private readonly bool $loggingEnabled = false,
        private readonly string $logLevel = 'debug',
        private readonly ?LoggerInterface $logger = null
    ) {
        $this->httpClient = $this->buildHttpClient();
    }

    public function get(string $endpoint, array $query = []): array
    {
        $this->log('GET', $endpoint, ['query' => $query]);

        $response = $this->httpClient->get($endpoint, $query);

        return $this->handleResponse($response, 'GET', $endpoint);
    }

    public function post(string $endpoint, array $data = []): array
    {
        $this->log('POST', $endpoint, ['data' => $data]);

        $response = $this->httpClient->post($endpoint, $data);

        return $this->handleResponse($response, 'POST', $endpoint);
    }

    public function put(string $endpoint, array $data = []): array
    {
        $this->log('PUT', $endpoint, ['data' => $data]);

        $response = $this->httpClient->put($endpoint, $data);

        return $this->handleResponse($response, 'PUT', $endpoint);
    }

    public function delete(string $endpoint): array
    {
        $this->log('DELETE', $endpoint);

        $response = $this->httpClient->delete($endpoint);

        return $this->handleResponse($response, 'DELETE', $endpoint);
    }

    private function buildHttpClient(): PendingRequest
    {
        /** @var PendingRequest $client */
        $client = $this->httpFactory
            ->baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry(
                $this->retryConfig['times'],
                $this->retryConfig['sleep']
            );

        // Connect API doesn't use API key - it's public
        $client->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        return $client;
    }

    private function handleResponse(Response $response, string $method, string $endpoint): array
    {
        $statusCode = $response->status();
        $body = $response->body();

        $this->log('Response', $endpoint, [
            'method' => $method,
            'status' => $statusCode,
            'body' => $body
        ]);

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        // Handle Connect API specific errors
        $errorData = $response->json() ?? [];

        throw new ConnectApiException(
            message: $errorData['message'] ?? 'Connect API request failed',
            httpStatusCode: $statusCode,
            errorCode: $errorData['errorCode'] ?? null,
            errorDetails: $errorData['errorDetails'] ?? [],
            previous: null
        );
    }

    private function log(string $action, string $endpoint, array $context = []): void
    {
        if (!$this->loggingEnabled) {
            return;
        }

        $logger = $this->logger ?? Log::channel();

        $message = "Magicline Connect API {$action}: {$endpoint}";

        $logger->log($this->logLevel, $message, $context);
    }
}