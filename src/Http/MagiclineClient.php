<?php

namespace AlexBabintsev\Magicline\Http;

use AlexBabintsev\Magicline\Exceptions\MagiclineApiException;
use AlexBabintsev\Magicline\Exceptions\MagiclineAuthenticationException;
use AlexBabintsev\Magicline\Exceptions\MagiclineAuthorizationException;
use AlexBabintsev\Magicline\Exceptions\MagiclineValidationException;
use AlexBabintsev\Magicline\Traits\LogsApiOperations;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class MagiclineClient
{
    use LogsApiOperations;

    private PendingRequest $httpClient;

    public function __construct(
        private readonly Factory $httpFactory,
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly int $timeout = 30,
        private readonly array $retryConfig = ['times' => 3, 'sleep' => 100],
        private readonly bool $loggingEnabled = false,
        private readonly string $logLevel = 'debug',
        private readonly ?LoggerInterface $logger = null
    ) {
        $this->httpClient = $this->buildHttpClient();
    }

    private function buildHttpClient(): PendingRequest
    {
        /** @var PendingRequest $client */
        $client = $this->httpFactory->baseUrl($this->baseUrl);

        return $client
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->retry(
                $this->retryConfig['times'],
                $this->retryConfig['sleep'],
                when: fn ($exception, $request) => $this->shouldRetry($exception, $request)
            );
    }

    public function get(string $uri, array $query = []): array
    {
        return $this->makeRequest('GET', $uri, $query);
    }

    public function post(string $uri, array $data = []): array
    {
        return $this->makeRequest('POST', $uri, [], $data);
    }

    public function put(string $uri, array $data = []): array
    {
        return $this->makeRequest('PUT', $uri, [], $data);
    }

    public function patch(string $uri, array $data = []): array
    {
        return $this->makeRequest('PATCH', $uri, [], $data);
    }

    public function delete(string $uri): array
    {
        return $this->makeRequest('DELETE', $uri);
    }

    private function makeRequest(string $method, string $uri, array $query = [], array $data = []): array
    {
        return $this->executeWithDatabaseLogging($method, $uri, $query, $data);
    }

    private function executeWithDatabaseLogging(string $method, string $uri, array $query = [], array $data = []): array
    {
        // Extract resource info from URI for logging
        $resourceInfo = $this->parseResourceFromUri($uri);
        $requestData = $this->sanitizeRequestData(array_merge($query, $data));

        return $this->executeWithLogging(
            $resourceInfo['type'],
            strtolower($method),
            function () use ($method, $uri, $query, $data) {
                return $this->makeHttpRequest($method, $uri, $query, $data);
            },
            $resourceInfo['id'],
            $requestData
        );
    }

    private function makeHttpRequest(string $method, string $uri, array $query = [], array $data = []): array
    {
        $this->logRequest($method, $uri, $query, $data);

        try {
            $response = match (strtoupper($method)) {
                'GET' => $this->httpClient->get($uri, $query),
                'POST' => $this->httpClient->post($uri, $data),
                'PUT' => $this->httpClient->put($uri, $data),
                'PATCH' => $this->httpClient->patch($uri, $data),
                'DELETE' => $this->httpClient->delete($uri),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}")
            };

            $this->logResponse($response);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            $this->logError($e, $method, $uri);
            throw $e;
        }
    }

    private function handleResponse(Response $response): array
    {
        $statusCode = $response->status();
        $body = $response->json();

        if ($response->successful()) {
            return $body ?? [];
        }

        $errorMessage = $body['message'] ?? $body['error'] ?? 'Unknown error occurred';
        $errorCode = $body['code'] ?? null;

        match ($statusCode) {
            400 => throw new MagiclineValidationException($errorMessage, $statusCode, $errorCode, $body),
            401 => throw new MagiclineAuthenticationException($errorMessage, $statusCode, $errorCode, $body),
            403 => throw new MagiclineAuthorizationException($errorMessage, $statusCode, $errorCode, $body),
            default => throw new MagiclineApiException($errorMessage, $statusCode, $errorCode, $body)
        };
    }

    private function shouldRetry(\Exception $exception, $request): bool
    {
        if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
            return true;
        }

        if ($exception instanceof \Illuminate\Http\Client\RequestException) {
            $statusCode = $exception->response->status();

            return in_array($statusCode, [429, 500, 502, 503, 504]);
        }

        return false;
    }

    private function logRequest(string $method, string $uri, array $query, array $data): void
    {
        if (! $this->loggingEnabled) {
            return;
        }

        $logData = [
            'method' => $method,
            'uri' => $uri,
            'query' => $query,
            'data' => $data,
        ];

        $this->log("Magicline API Request: {$method} {$uri}", $logData);
    }

    private function logResponse(Response $response): void
    {
        if (! $this->loggingEnabled) {
            return;
        }

        $logData = [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->json(),
        ];

        $this->log("Magicline API Response: {$response->status()}", $logData);
    }

    private function logError(\Exception $exception, string $method, string $uri): void
    {
        if (! $this->loggingEnabled) {
            return;
        }

        $logData = [
            'method' => $method,
            'uri' => $uri,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
        ];

        $this->log("Magicline API Error: {$exception->getMessage()}", $logData, 'error');
    }

    private function log(string $message, array $context = [], ?string $level = null): void
    {
        $logger = $this->logger ?? Log::getFacadeRoot();
        $logLevel = $level ?? $this->logLevel;

        $logger->{$logLevel}($message, $context);
    }

    /**
     * Parse resource type and ID from URI
     */
    private function parseResourceFromUri(string $uri): array
    {
        // Remove leading slash and split by segments
        $segments = explode('/', trim($uri, '/'));

        // Default values
        $type = 'unknown';
        $id = null;

        if (empty($segments)) {
            return ['type' => $type, 'id' => $id];
        }

        // Map common URI patterns to resource types
        $resourceMap = [
            'customers' => 'customers',
            'appointments' => 'appointments',
            'classes' => 'classes',
            'employees' => 'employees',
            'devices' => 'devices',
            'memberships' => 'memberships',
            'studios' => 'studios',
            'finance' => 'finance',
            'payments' => 'payments',
            'checkin-vouchers' => 'checkin_vouchers',
            'trial-offers' => 'trial_offers',
        ];

        // Find resource type
        foreach ($segments as $segment) {
            if (isset($resourceMap[$segment])) {
                $type = $resourceMap[$segment];
                break;
            }
        }

        // Try to extract ID from URI
        // Look for numeric segments that might be IDs
        foreach ($segments as $segment) {
            if (is_numeric($segment) || $this->isUuid($segment)) {
                $id = $segment;
                break;
            }
        }

        return ['type' => $type, 'id' => $id];
    }

    /**
     * Check if string is a UUID
     */
    private function isUuid(string $string): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $string) === 1;
    }

    /**
     * Check if database logging is enabled
     */
    protected function isDatabaseLoggingEnabled(): bool
    {
        return config('magicline.logging.database.enabled', false);
    }
}
