<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Devices;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new Devices($this->mockClient);
});

test('list devices', function () {
    $expectedResponse = ['data' => ['devices']];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/devices')
        ->andReturn($expectedResponse);

    $result = $this->resource->list();

    expect($result)->toBe($expectedResponse);
});

test('activate device', function () {
    $deviceId = 'device-123';
    $expectedResponse = ['success' => true];

    $this->mockClient
        ->shouldReceive('post')
        ->once()
        ->with("/v1/devices/{$deviceId}/activate")
        ->andReturn($expectedResponse);

    $result = $this->resource->activate($deviceId);

    expect($result)->toBe($expectedResponse);
});
