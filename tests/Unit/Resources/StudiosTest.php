<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Studios;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new Studios($this->mockClient);
});

test('get utilization', function () {
    $expectedResponse = [
        'data' => [
            ['studioId' => 1, 'utilization' => 85.5],
            ['studioId' => 2, 'utilization' => 92.3],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/studios/utilization')
        ->andReturn($expectedResponse);

    $result = $this->resource->getUtilization();

    expect($result)->toBe($expectedResponse);
});