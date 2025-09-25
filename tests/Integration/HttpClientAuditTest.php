<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Models\MagiclineLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Enable database logging for tests
    Config::set('magicline.logging.database.enabled', true);

    $this->httpFactory = mock(Factory::class);
    $this->pendingRequest = mock(\Illuminate\Http\Client\PendingRequest::class);

    // Mock the factory chain
    $this->httpFactory
        ->shouldReceive('baseUrl')
        ->andReturn($this->pendingRequest);

    $this->pendingRequest
        ->shouldReceive('withHeaders')
        ->andReturnSelf();

    $this->pendingRequest
        ->shouldReceive('timeout')
        ->andReturnSelf();

    $this->pendingRequest
        ->shouldReceive('retry')
        ->andReturnSelf();

    $this->client = new MagiclineClient(
        $this->httpFactory,
        'test-api-key',
        'https://api.example.com',
        30,
        ['times' => 3, 'sleep' => 100],
        false, // file logging disabled for cleaner tests
        'debug'
    );
});

it('logs successful GET requests to database', function () {
    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('status')->andReturn(200);
    $mockResponse->shouldReceive('successful')->andReturn(true);
    $mockResponse->shouldReceive('json')->andReturn(['id' => 123, 'name' => 'John Doe']);

    $this->pendingRequest
        ->shouldReceive('get')
        ->with('customers/123', [])
        ->andReturn($mockResponse);

    $result = $this->client->get('customers/123');

    // Check API result
    expect($result)->toBe(['id' => 123, 'name' => 'John Doe']);

    // Check database logging
    $log = MagiclineLog::first();
    expect($log)->not->toBeNull();
    expect($log->resource_type)->toBe('customers');
    expect($log->resource_id)->toBe('123');
    expect($log->action)->toBe('get');
    expect($log->status)->toBe(MagiclineLog::STATUS_SUCCESS);
    expect($log->response_data)->toBe(['id' => 123, 'name' => 'John Doe']);
});

it('logs successful POST requests to database', function () {
    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('status')->andReturn(201);
    $mockResponse->shouldReceive('successful')->andReturn(true);
    $mockResponse->shouldReceive('json')->andReturn(['id' => 456, 'created' => true]);

    $requestData = ['name' => 'Jane Smith', 'email' => 'jane@example.com'];

    $this->pendingRequest
        ->shouldReceive('post')
        ->with('customers', $requestData)
        ->andReturn($mockResponse);

    $result = $this->client->post('customers', $requestData);

    // Check API result
    expect($result)->toBe(['id' => 456, 'created' => true]);

    // Check database logging
    $log = MagiclineLog::first();
    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('post');
    expect($log->status)->toBe(MagiclineLog::STATUS_SUCCESS);
    expect($log->request_data)->toBe($requestData);
    expect($log->response_data)->toBe(['id' => 456, 'created' => true]);
});

it('logs failed requests to database', function () {
    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('status')->andReturn(400);
    $mockResponse->shouldReceive('successful')->andReturn(false);
    $mockResponse->shouldReceive('json')->andReturn(['error' => 'Validation failed']);

    $this->pendingRequest
        ->shouldReceive('get')
        ->with('customers/999', [])
        ->andReturn($mockResponse);

    try {
        $this->client->get('customers/999');
    } catch (Exception $e) {
        // Expected to throw exception
    }

    // Check database logging
    $log = MagiclineLog::first();
    expect($log)->not->toBeNull();
    expect($log->resource_type)->toBe('customers');
    expect($log->resource_id)->toBe('999');
    expect($log->action)->toBe('get');
    expect($log->status)->toBe(MagiclineLog::STATUS_FAILED);
    expect($log->error_message)->toBe('Validation failed');
});

it('does not log when database logging is disabled', function () {
    // Disable database logging
    Config::set('magicline.logging.database.enabled', false);

    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('status')->andReturn(200);
    $mockResponse->shouldReceive('successful')->andReturn(true);
    $mockResponse->shouldReceive('json')->andReturn(['id' => 123]);

    $this->pendingRequest
        ->shouldReceive('get')
        ->with('customers/123', [])
        ->andReturn($mockResponse);

    $this->client->get('customers/123');

    // Should not create any log entries
    expect(MagiclineLog::count())->toBe(0);
});

it('sanitizes sensitive data in logs', function () {
    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('status')->andReturn(200);
    $mockResponse->shouldReceive('successful')->andReturn(true);
    $mockResponse->shouldReceive('json')->andReturn(['token' => 'secret-token-123']);

    $sensitiveData = [
        'username' => 'john',
        'password' => 'secret123',
        'api_key' => 'api-key-456',
    ];

    $this->pendingRequest
        ->shouldReceive('post')
        ->with('auth/login', $sensitiveData)
        ->andReturn($mockResponse);

    $this->client->post('auth/login', $sensitiveData);

    $log = MagiclineLog::first();
    expect($log->request_data['username'])->toBe('john');
    expect($log->request_data['password'])->toBe('***REDACTED***');
    expect($log->request_data['api_key'])->toBe('***REDACTED***');
    expect($log->response_data['token'])->toBe('***REDACTED***');
});

it('can generate audit reports from logs', function () {
    // Create test data
    MagiclineLog::logSuccess('customers', 'create', '1');
    MagiclineLog::logSuccess('customers', 'update', '1');
    MagiclineLog::logError('customers', 'delete', 'Not found', '2');
    MagiclineLog::logSuccess('appointments', 'create', '10');

    // Test filtering by resource type
    $customerLogs = MagiclineLog::forResource('customers')->get();
    expect($customerLogs)->toHaveCount(3);

    // Test success rate calculation
    $stats = MagiclineLog::getStats('customers');
    expect($stats['success_rate'])->toBe(66.67); // 2 success out of 3 total

    // Test overall statistics
    $overallStats = MagiclineLog::getStats();
    expect($overallStats['total'])->toBe(4);
    expect($overallStats['successful'])->toBe(3);
    expect($overallStats['failed'])->toBe(1);
});
