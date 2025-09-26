<?php

namespace AlexBabintsev\Magicline\Connect\Http;

use AlexBabintsev\Magicline\Connect\Exceptions\ConnectApiException;
use AlexBabintsev\Magicline\Traits\LogsApiOperations;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class MagiclineConnectClient
{
    use LogsApiOperations;

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
        return $this->executeWithDatabaseLogging('GET', $endpoint, $query);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->executeWithDatabaseLogging('POST', $endpoint, $data);
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->executeWithDatabaseLogging('PUT', $endpoint, $data);
    }

    public function delete(string $endpoint): array
    {
        return $this->executeWithDatabaseLogging('DELETE', $endpoint);
    }

    private function executeWithDatabaseLogging(string $method, string $endpoint, array $data = []): array
    {
        $resourceInfo = $this->parseConnectResourceFromUri($endpoint);
        $requestData = $this->sanitizeRequestData($data);

        return $this->executeWithLogging(
            "connect_{$resourceInfo['type']}",
            strtolower($method),
            function () use ($method, $endpoint, $data) {
                return $this->makeHttpRequest($method, $endpoint, $data);
            },
            $resourceInfo['id'],
            $requestData
        );
    }

    private function makeHttpRequest(string $method, string $endpoint, array $data = []): array
    {
        $logData = $method === 'GET' ? ['query' => $data] : ['data' => $data];
        $this->log($method, $endpoint, $logData);

        $response = match ($method) {
            'GET' => $this->httpClient->get($endpoint, $data),
            'POST' => $this->httpClient->post($endpoint, $data),
            'PUT' => $this->httpClient->put($endpoint, $data),
            'DELETE' => $this->httpClient->delete($endpoint),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}")
        };

        return $this->handleResponse($response, $method, $endpoint);
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
            'body' => $body,
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
        if (! $this->loggingEnabled) {
            return;
        }

        $logger = $this->logger ?? Log::channel();

        $message = "Magicline Connect API {$action}: {$endpoint}";

        $logger->log($this->logLevel, $message, $context);
    }

    /**
     * Parse Connect API resource from URI
     */
    private function parseConnectResourceFromUri(string $uri): array
    {
        $segments = explode('/', trim($uri, '/'));

        $connectResourceMap = [
            'studios' => 'studios',
            'campaigns' => 'campaigns',
            'referrals' => 'referrals',
            'leads' => 'leads',
            'trial-sessions' => 'trial_sessions',
            'rate-bundles' => 'rate_bundles',
            'contracts' => 'contracts',
            'credit-card-tokenization' => 'credit_card_tokenization',
            'image-upload' => 'image_upload',
            'validation' => 'validation',
            'address-data' => 'address_data',
        ];

        $type = 'unknown';
        $id = null;

        foreach ($segments as $segment) {
            if (isset($connectResourceMap[$segment])) {
                $type = $connectResourceMap[$segment];
                break;
            }
        }

        // Extract ID
        foreach ($segments as $segment) {
            if (is_numeric($segment) || $this->isUuid($segment)) {
                $id = $segment;
                break;
            }
        }

        return ['type' => $type, 'id' => $id];
    }

    /**
     * Check if database logging is enabled for Connect API
     */
    protected function isDatabaseLoggingEnabled(): bool
    {
        return config('magicline.connect.logging.database.enabled', false);
    }

    /**
     * Check if string is a UUID
     */
    private function isUuid(string $string): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $string) === 1;
    }
}
