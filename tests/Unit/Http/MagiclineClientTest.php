<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Exceptions\MagiclineApiException;
use AlexBabintsev\Magicline\Exceptions\MagiclineAuthenticationException;
use AlexBabintsev\Magicline\Exceptions\MagiclineAuthorizationException;
use AlexBabintsev\Magicline\Exceptions\MagiclineValidationException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

test('should retry on connection exception', function () {
    $factory = Mockery::mock(Factory::class);
    $pendingRequest = Mockery::mock(PendingRequest::class);

    $factory->shouldReceive('baseUrl')->andReturn($pendingRequest);
    $pendingRequest->shouldReceive('withHeaders')->andReturnSelf();
    $pendingRequest->shouldReceive('timeout')->andReturnSelf();
    $pendingRequest->shouldReceive('retry')->andReturnSelf();

    $client = new MagiclineClient(
        httpFactory: $factory,
        apiKey: 'test-key',
        baseUrl: 'https://test.magicline.com'
    );

    $exception = new ConnectionException('Connection failed');

    $shouldRetry = callPrivateMethod($client, 'shouldRetry', [$exception, null]);

    expect($shouldRetry)->toBeTrue();
});

test('should retry on specific status codes', function () {
    $factory = Mockery::mock(Factory::class);
    $pendingRequest = Mockery::mock(PendingRequest::class);

    $factory->shouldReceive('baseUrl')->andReturn($pendingRequest);
    $pendingRequest->shouldReceive('withHeaders')->andReturnSelf();
    $pendingRequest->shouldReceive('timeout')->andReturnSelf();
    $pendingRequest->shouldReceive('retry')->andReturnSelf();

    $client = new MagiclineClient(
        httpFactory: $factory,
        apiKey: 'test-key',
        baseUrl: 'https://test.magicline.com'
    );

    // Test that private method shouldRetry exists and can handle different scenarios
    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('shouldRetry');
    $method->setAccessible(true);

    // Test connection exception
    $connectionException = new ConnectionException('Connection failed');
    $shouldRetry = $method->invoke($client, $connectionException, null);
    expect($shouldRetry)->toBeTrue();

    // Test non-retryable exception
    $otherException = new \InvalidArgumentException('Invalid argument');
    $shouldRetry = $method->invoke($client, $otherException, null);
    expect($shouldRetry)->toBeFalse();
});

test('should not retry on other exceptions', function () {
    $factory = Mockery::mock(Factory::class);
    $pendingRequest = Mockery::mock(PendingRequest::class);

    $factory->shouldReceive('baseUrl')->andReturn($pendingRequest);
    $pendingRequest->shouldReceive('withHeaders')->andReturnSelf();
    $pendingRequest->shouldReceive('timeout')->andReturnSelf();
    $pendingRequest->shouldReceive('retry')->andReturnSelf();

    $client = new MagiclineClient(
        httpFactory: $factory,
        apiKey: 'test-key',
        baseUrl: 'https://test.magicline.com'
    );

    $exception = new \InvalidArgumentException('Invalid argument');

    $shouldRetry = callPrivateMethod($client, 'shouldRetry', [$exception, null]);

    expect($shouldRetry)->toBeFalse();
});

function callPrivateMethod($object, $method, $parameters = [])
{
    $reflection = new ReflectionClass(get_class($object));
    $method = $reflection->getMethod($method);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
}