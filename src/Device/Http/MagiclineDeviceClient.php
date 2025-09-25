<?php

namespace AlexBabintsev\Magicline\Device\Http;

use AlexBabintsev\Magicline\Exceptions\MagiclineApiException;
use AlexBabintsev\Magicline\Exceptions\MagiclineAuthenticationException;
use AlexBabintsev\Magicline\Traits\LogsApiOperations;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class MagiclineDeviceClient
{
    use LogsApiOperations;

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
        return $this->executeWithDatabaseLogging('GET', $endpoint, $query, $headers);
    }

    /**
     * Make a POST request
     */
    public function post(string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->executeWithDatabaseLogging('POST', $endpoint, $data, $headers);
    }

    private function executeWithDatabaseLogging(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $resourceInfo = $this->parseDeviceResourceFromUri($endpoint);
        $requestData = $this->sanitizeRequestData($data);

        return $this->executeWithLogging(
            "device_{$resourceInfo['type']}",
            strtolower($method),
            function () use ($method, $endpoint, $data, $headers) {
                return $this->makeRequest($method, $endpoint, $data, [], $headers);
            },
            $resourceInfo['id'],
            $requestData
        );
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

    /**
     * Parse Device API resource from URI
     */
    private function parseDeviceResourceFromUri(string $uri): array
    {
        $segments = explode('/', trim($uri, '/'));

        $deviceResourceMap = [
            'access' => 'access',
            'card-reader' => 'access',
            'vending' => 'vending',
            'time' => 'time',
        ];

        $type = 'unknown';
        $id = null;

        foreach ($segments as $segment) {
            if (isset($deviceResourceMap[$segment])) {
                $type = $deviceResourceMap[$segment];
                break;
            }
        }

        // Extract ID if present
        foreach ($segments as $segment) {
            if (is_numeric($segment) || $this->isUuid($segment)) {
                $id = $segment;
                break;
            }
        }

        return ['type' => $type, 'id' => $id];
    }

    /**
     * Check if database logging is enabled for Device API
     */
    protected function isDatabaseLoggingEnabled(): bool
    {
        return config('magicline.device.logging.database.enabled', false);
    }

    /**
     * Check if string is a UUID
     */
    private function isUuid(string $string): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $string) === 1;
    }
}