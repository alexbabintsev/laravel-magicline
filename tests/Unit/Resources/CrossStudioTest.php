<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CrossStudio;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
    $this->resource = new CrossStudio($this->client);
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

    $this->client
        ->expects($this->once())
        ->method('get')
        ->with('/v1/cross-studio/customers/by', $criteria)
        ->willReturn($expectedResponse);

    $result = $this->resource->getCustomersBy($criteria);

    expect($result)->toBe($expectedResponse);
});
