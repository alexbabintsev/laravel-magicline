<?php

namespace AlexBabintsev\Magicline\Device\Http;

use AlexBabintsev\Magicline\Exceptions\MagiclineApiException;
use AlexBabintsev\Magicline\Exceptions\MagiclineAuthenticationException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class MagiclineDeviceClient
{
    public function __construct(
        private readonly Factory $httpFactory,
        private readonly string $baseUrl,
        private readonly string $bearerToken,
        private readonly int $timeout = 30,
        private readonly int $retryTimes = 3,
        private readonly int $retryDelay = 1000,
        private readonly ?LoggerInterface $logger = null,
        private readonly bool $loggingEnabled = true
    ) {
    }

    /**
     * Make a GET request
     */
    public function get(string $endpoint, array $query = [], array $headers = []): array
    {
        return $this->makeRequest('GET', $endpoint, [], $query, $headers);
    }

    /**
     * Make a POST request
     */
    public function post(string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->makeRequest('POST', $endpoint, $data, [], $headers);
    }

    /**
     * Make HTTP request to Device API
     */
    private function makeRequest(
        string $method,
        string $endpoint,
        array $data = [],
        array $query = [],
        array $headers = []
    ): array {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->bearerToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $finalHeaders = array_merge($defaultHeaders, $headers);

        if ($this->loggingEnabled && $this->logger) {
            $this->logger->debug('Magicline Device API Request', [
                'method' => $method,
                'url' => $url,
                'data' => $data ?: null,
            ]);
        }

        $client = $this->httpFactory
            ->withHeaders($finalHeaders)
            ->timeout($this->timeout)
            ->retry($this->retryTimes, $this->retryDelay);

        $response = match ($method) {
            'GET' => $client->get($url, $query),
            'POST' => $client->post($url, $data),
            default => $client->send($method, $url, [
                'json' => $data,
                'query' => $query,
            ]),
        };

        return $this->handleResponse($response, $method, $endpoint);
    }

    /**
     * Handle the HTTP response
     */
    private function handleResponse(Response $response, string $method, string $endpoint): array
    {
        $responseData = $response->json() ?? [];

        if ($this->loggingEnabled && $this->logger) {
            $this->logger->debug('Magicline Device API Response', $responseData);
        }

        if ($response->unauthorized()) {
            throw new MagiclineAuthenticationException(
                'Device API authentication failed. Check your bearer token.',
                $response->status()
            );
        }

        if ($response->failed()) {
            $errorData = $response->json();

            throw new MagiclineApiException(
                $errorData['errorMessage'] ?? 'Device API request failed',
                $response->status(),
                $errorData['errorCode'] ?? null,
                $errorData
            );
        }

        // Handle 204 No Content responses
        if ($response->status() === 204) {
            return [];
        }

        return $responseData;
    }

    /**
     * Sanitize headers for logging (remove sensitive data)
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = $headers;

        if (isset($sanitized['Authorization'])) {
            $sanitized['Authorization'] = 'Bearer ***';
        }

        return $sanitized;
    }

    /**
     * Get the base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get timeout setting
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Check if logging is enabled
     */
    public function isLoggingEnabled(): bool
    {
        return $this->loggingEnabled;
    }
}