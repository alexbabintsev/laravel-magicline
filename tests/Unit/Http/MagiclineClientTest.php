<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;

test('magicline client can be instantiated', function () {
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

    expect($client)->toBeInstanceOf(MagiclineClient::class);
});
