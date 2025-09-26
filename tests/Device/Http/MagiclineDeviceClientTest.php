<?php

use AlexBabintsev\Magicline\Device\Http\MagiclineDeviceClient;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->httpFactory = mock(Factory::class);
    $this->logger = mock(LoggerInterface::class);

    $this->client = new MagiclineDeviceClient(
        $this->httpFactory,
        'https://api.example.com',
        'test-bearer-token',
        30,
        3,
        1000,
        $this->logger,
        false // disable logging to avoid mock expectations
    );
});

it('can make GET requests', function () {
    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('json')->once()->andReturn(['data' => 'test']);
    $mockResponse->shouldReceive('unauthorized')->once()->andReturn(false);
    $mockResponse->shouldReceive('failed')->once()->andReturn(false);
    $mockResponse->shouldReceive('status')->once()->andReturn(200);

    $this->httpFactory
        ->shouldReceive('withHeaders')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('timeout')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('retry')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('get')
        ->once()
        ->andReturn($mockResponse);

    $result = $this->client->get('test-endpoint');

    expect($result)->toBe(['data' => 'test']);
});

it('can make POST requests with data', function () {
    $postData = ['key' => 'value'];

    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('json')->once()->andReturn(['success' => true]);
    $mockResponse->shouldReceive('unauthorized')->once()->andReturn(false);
    $mockResponse->shouldReceive('failed')->once()->andReturn(false);
    $mockResponse->shouldReceive('status')->once()->andReturn(200);

    $this->httpFactory
        ->shouldReceive('withHeaders')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('timeout')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('retry')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('post')
        ->once()
        ->andReturn($mockResponse);

    $result = $this->client->post('test-endpoint', $postData);

    expect($result)->toBe(['success' => true]);
});

it('logs requests when logging is enabled', function () {
    $logger = mock(LoggerInterface::class);

    $client = new MagiclineDeviceClient(
        $this->httpFactory,
        'https://api.example.com',
        'test-bearer-token',
        30,
        3,
        1000,
        $logger,
        true // enable logging
    );

    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('json')->once()->andReturn(['data' => 'test']);
    $mockResponse->shouldReceive('unauthorized')->once()->andReturn(false);
    $mockResponse->shouldReceive('failed')->once()->andReturn(false);
    $mockResponse->shouldReceive('status')->once()->andReturn(200);

    $this->httpFactory
        ->shouldReceive('withHeaders')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('timeout')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('retry')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('get')
        ->once()
        ->andReturn($mockResponse);

    $logger
        ->shouldReceive('debug')
        ->once()
        ->with('Magicline Device API Request', [
            'method' => 'GET',
            'url' => 'https://api.example.com/test-endpoint',
            'data' => null,
        ]);

    $logger
        ->shouldReceive('debug')
        ->once()
        ->with('Magicline Device API Response', ['data' => 'test']);

    $client->get('test-endpoint');
});

it('does not log when logging is disabled', function () {
    $client = new MagiclineDeviceClient(
        $this->httpFactory,
        'https://api.example.com',
        'test-bearer-token',
        30,
        3,
        1000,
        $this->logger,
        false // logging disabled
    );

    $mockResponse = mock(Response::class);
    $mockResponse->shouldReceive('json')->once()->andReturn(['data' => 'test']);
    $mockResponse->shouldReceive('unauthorized')->once()->andReturn(false);
    $mockResponse->shouldReceive('failed')->once()->andReturn(false);
    $mockResponse->shouldReceive('status')->once()->andReturn(200);

    $this->httpFactory
        ->shouldReceive('withHeaders')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('timeout')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('retry')
        ->once()
        ->andReturnSelf();

    $this->httpFactory
        ->shouldReceive('get')
        ->once()
        ->andReturn($mockResponse);

    $this->logger
        ->shouldNotReceive('debug');

    $client->get('test-endpoint');
});
