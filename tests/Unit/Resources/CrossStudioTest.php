<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\CrossStudio;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new CrossStudio($this->mockClient);
});

test('get customers by', function () {
    $criteria = [
        'email' => 'test@example.com',
        'phone' => '+49123456789',
    ];

    $expectedResponse = [
        'data' => [
            ['id' => 1, 'firstName' => 'John', 'email' => 'test@example.com'],
            ['id' => 2, 'firstName' => 'Jane', 'phone' => '+49123456789'],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/cross-studio/customers/by', $criteria)
        ->andReturn($expectedResponse);

    $result = $this->resource->getCustomersBy($criteria);

    expect($result)->toBe($expectedResponse);
});
