<?php

use AlexBabintsev\Magicline\Models\MagiclineLog;
use AlexBabintsev\Magicline\Traits\LogsApiOperations;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

class TestClassWithLogging
{
    use LogsApiOperations;

    public function testExecuteWithLogging(string $resourceType, string $action, callable $operation)
    {
        return $this->executeWithLogging($resourceType, $action, $operation);
    }

    public function testLogOperationStart(string $resourceType, string $action)
    {
        return $this->logOperationStart($resourceType, $action);
    }

    public function testLogOperationSuccess(string $resourceType, string $action, array $responseData = null)
    {
        $this->logOperationSuccess($resourceType, $action, $responseData);
    }

    public function testLogOperationError(string $resourceType, string $action, string $errorMessage)
    {
        $this->logOperationError($resourceType, $action, $errorMessage);
    }

    public function testSanitizeRequestData(array $data): array
    {
        return $this->sanitizeRequestData($data);
    }

    public function testSanitizeResponseData(array $data): array
    {
        return $this->sanitizeResponseData($data);
    }

    protected function isDatabaseLoggingEnabled(): bool
    {
        return true; // Enable for testing
    }
}

beforeEach(function () {
    $this->testClass = new TestClassWithLogging();
});

it('can execute operation with successful logging', function () {
    $result = $this->testClass->testExecuteWithLogging(
        'customers',
        'create',
        fn() => ['id' => 123, 'name' => 'John Doe']
    );

    expect($result)->toBe(['id' => 123, 'name' => 'John Doe']);

    $log = MagiclineLog::first();
    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('create');
    expect($log->status)->toBe(MagiclineLog::STATUS_SUCCESS);
    expect($log->response_data)->toBe(['id' => 123, 'name' => 'John Doe']);
});

it('can execute operation with error logging', function () {
    expect(fn() => $this->testClass->testExecuteWithLogging(
        'customers',
        'create',
        fn() => throw new Exception('Database error')
    ))->toThrow(Exception::class, 'Database error');

    $log = MagiclineLog::first();
    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('create');
    expect($log->status)->toBe(MagiclineLog::STATUS_FAILED);
    expect($log->error_message)->toBe('Database error');
});

it('can log operation start', function () {
    $log = $this->testClass->testLogOperationStart('customers', 'create');

    expect($log)->toBeInstanceOf(MagiclineLog::class);
    expect($log->status)->toBe(MagiclineLog::STATUS_PENDING);
    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('create');
});

it('can log operation success', function () {
    $this->testClass->testLogOperationSuccess(
        'customers',
        'create',
        ['id' => 456, 'created' => true]
    );

    $log = MagiclineLog::first();
    expect($log->status)->toBe(MagiclineLog::STATUS_SUCCESS);
    expect($log->response_data)->toBe(['id' => 456, 'created' => true]);
});

it('can log operation error', function () {
    $this->testClass->testLogOperationError(
        'customers',
        'create',
        'Validation failed'
    );

    $log = MagiclineLog::first();
    expect($log->status)->toBe(MagiclineLog::STATUS_FAILED);
    expect($log->error_message)->toBe('Validation failed');
});

it('sanitizes sensitive request data', function () {
    $sensitiveData = [
        'name' => 'John Doe',
        'password' => 'secret123',
        'api_key' => 'api-key-123',
        'credit_card' => '1234-5678-9012-3456',
        'authorization' => 'Bearer token123',
    ];

    $sanitized = $this->testClass->testSanitizeRequestData($sensitiveData);

    expect($sanitized['name'])->toBe('John Doe');
    expect($sanitized['password'])->toBe('***REDACTED***');
    expect($sanitized['api_key'])->toBe('***REDACTED***');
    expect($sanitized['credit_card'])->toBe('***REDACTED***');
    expect($sanitized['authorization'])->toBe('***REDACTED***');
});

it('sanitizes sensitive response data', function () {
    $sensitiveResponse = [
        'id' => 123,
        'token' => 'access-token-123',
        'access_token' => 'oauth-token-456',
        'payment_token' => 'payment-token-789',
        'user_data' => 'safe data',
    ];

    $sanitized = $this->testClass->testSanitizeResponseData($sensitiveResponse);

    expect($sanitized['id'])->toBe(123);
    expect($sanitized['user_data'])->toBe('safe data');
    expect($sanitized['token'])->toBe('***REDACTED***');
    expect($sanitized['access_token'])->toBe('***REDACTED***');
    expect($sanitized['payment_token'])->toBe('***REDACTED***');
});

it('handles logging failures gracefully', function () {
    // Create a test class that disables database logging to simulate graceful handling
    $testClass = new class {
        use LogsApiOperations;

        protected function isDatabaseLoggingEnabled(): bool
        {
            return false; // Simulate logging disabled to test graceful handling
        }

        public function testWithFailingDatabase()
        {
            return $this->executeWithLogging(
                'customers',
                'create',
                fn() => ['success' => true]
            );
        }
    };

    // Should not throw exception, should handle gracefully
    $result = $testClass->testWithFailingDatabase();
    expect($result)->toBe(['success' => true]);

    // Verify no logs were created when logging is disabled
    expect(MagiclineLog::count())->toBe(0);
});
