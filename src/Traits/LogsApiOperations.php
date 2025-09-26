<?php

namespace AlexBabintsev\Magicline\Traits;

use AlexBabintsev\Magicline\Models\MagiclineLog;
use Illuminate\Support\Facades\Config;
use Throwable;

trait LogsApiOperations
{
    /**
     * Log API operation start
     */
    protected function logOperationStart(
        string $resourceType,
        string $action,
        ?string $resourceId = null,
        ?array $requestData = null
    ): ?MagiclineLog {
        if (!$this->isDatabaseLoggingEnabled()) {
            return null;
        }

        try {
            return MagiclineLog::logPending($resourceType, $action, $resourceId, $requestData);
        } catch (Throwable $e) {
            // Don't let logging failures break the main operation
            $this->logToFile('Failed to log operation start', [
                'error' => $e->getMessage(),
                'resource_type' => $resourceType,
                'action' => $action,
            ]);

            return null;
        }
    }

    /**
     * Log successful API operation
     */
    protected function logOperationSuccess(
        string $resourceType,
        string $action,
        ?array $responseData = null,
        ?string $resourceId = null,
        ?array $requestData = null,
        ?MagiclineLog $existingLog = null
    ): void {
        if (!$this->isDatabaseLoggingEnabled()) {
            return;
        }

        try {
            $sanitizedResponseData = $responseData ? $this->sanitizeResponseData($responseData) : null;
            $sanitizedRequestData = $requestData ? $this->sanitizeRequestData($requestData) : null;

            if ($existingLog) {
                $existingLog->updateWithResponse($sanitizedResponseData ?? [], true);
            } else {
                MagiclineLog::logSuccess(
                    $resourceType,
                    $action,
                    $resourceId,
                    $sanitizedRequestData,
                    $sanitizedResponseData
                );
            }
        } catch (Throwable $e) {
            $this->logToFile('Failed to log operation success', [
                'error' => $e->getMessage(),
                'resource_type' => $resourceType,
                'action' => $action,
            ]);
        }
    }

    /**
     * Log failed API operation
     */
    protected function logOperationError(
        string $resourceType,
        string $action,
        string $errorMessage,
        ?string $resourceId = null,
        ?array $requestData = null,
        ?array $responseData = null,
        ?MagiclineLog $existingLog = null
    ): void {
        if (!$this->isDatabaseLoggingEnabled()) {
            return;
        }

        try {
            $sanitizedResponseData = $responseData ? $this->sanitizeResponseData($responseData) : null;
            $sanitizedRequestData = $requestData ? $this->sanitizeRequestData($requestData) : null;

            if ($existingLog) {
                $existingLog->updateWithError($errorMessage, $sanitizedResponseData);
            } else {
                MagiclineLog::logError(
                    $resourceType,
                    $action,
                    $errorMessage,
                    $resourceId,
                    $sanitizedRequestData,
                    $sanitizedResponseData
                );
            }
        } catch (Throwable $e) {
            $this->logToFile('Failed to log operation error', [
                'error' => $e->getMessage(),
                'resource_type' => $resourceType,
                'action' => $action,
                'original_error' => $errorMessage,
            ]);
        }
    }

    /**
     * Execute operation with comprehensive logging
     */
    protected function executeWithLogging(
        string $resourceType,
        string $action,
        callable $operation,
        ?string $resourceId = null,
        ?array $requestData = null
    ): mixed {
        $log = $this->logOperationStart($resourceType, $action, $resourceId, $requestData);

        try {
            $result = $operation();

            // Extract response data if it's an array or object
            $responseData = null;
            if (is_array($result)) {
                $responseData = $result;
            } elseif (is_object($result) && method_exists($result, 'toArray')) {
                $responseData = $result->toArray();
            }

            $this->logOperationSuccess(
                $resourceType,
                $action,
                $responseData,
                $resourceId,
                $requestData,
                $log
            );

            return $result;
        } catch (Throwable $e) {
            $this->logOperationError(
                $resourceType,
                $action,
                $e->getMessage(),
                $resourceId,
                $requestData,
                null,
                $log
            );

            throw $e;
        }
    }

    /**
     * Check if database logging is enabled
     */
    protected function isDatabaseLoggingEnabled(): bool
    {
        return Config::get('magicline.logging.database.enabled', false);
    }

    /**
     * Get resource ID from response data
     */
    protected function extractResourceId(array $responseData): ?string
    {
        // Try common ID fields
        foreach (['id', 'uuid', 'resourceId', 'customerId', 'appointmentId'] as $field) {
            if (isset($responseData[$field])) {
                return (string) $responseData[$field];
            }
        }

        // Try nested data
        if (isset($responseData['data']['id'])) {
            return (string) $responseData['data']['id'];
        }

        return null;
    }

    /**
     * Sanitize request data for logging
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'token',
            'api_key',
            'bearer_token',
            'authorization',
            'credit_card',
            'creditCard',
            'payment_info',
            'paymentInfo',
        ];

        $sanitized = $data;

        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '***REDACTED***';
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize response data for logging
     */
    protected function sanitizeResponseData(array $data): array
    {
        $sensitiveFields = [
            'token',
            'access_token',
            'refresh_token',
            'payment_token',
            'paymentToken',
            'credit_card_token',
            'creditCardToken',
        ];

        $sanitized = $data;

        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '***REDACTED***';
            }
        }

        return $sanitized;
    }

    /**
     * Log to file as fallback
     */
    protected function logToFile(string $message, array $context = []): void
    {
        if (!$this->isLoggingEnabled()) {
            return;
        }

        // Use Laravel's Log facade as fallback
        \Illuminate\Support\Facades\Log::error($message, $context);
    }

    /**
     * Check if file logging is enabled (fallback method)
     */
    protected function isLoggingEnabled(): bool
    {
        if (method_exists($this, 'loggingEnabled')) {
            return $this->loggingEnabled;
        }

        return Config::get('magicline.logging.enabled', false);
    }
}
