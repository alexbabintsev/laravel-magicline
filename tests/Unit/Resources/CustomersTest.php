<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Customers;

beforeEach(function () {
    $this->client = Mockery::mock(MagiclineClient::class);
    $this->resource = new Customers($this->client);
});

test('list without pagination', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 1, 'firstName' => 'John', 'lastName' => 'Doe'],
            ['id' => 2, 'firstName' => 'Jane', 'lastName' => 'Smith'],
        ],
    ];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with('/v1/customers', [])
        ->andReturn($expectedResponse);

    $result = $this->resource->list();

    expect($result)->toBe($expectedResponse);
});

test('list with pagination', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 3, 'firstName' => 'Bob', 'lastName' => 'Johnson'],
        ],
    ];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with('/v1/customers', ['offset' => '10', 'sliceSize' => 25])
        ->andReturn($expectedResponse);

    $result = $this->resource->list(10, 25);

    expect($result)->toBe($expectedResponse);
});

test('find', function () {
    $customerId = 123;
    $expectedResponse = [
        'data' => [
            'id' => 123,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
        ],
    ];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/v1/customers/{$customerId}")
        ->andReturn($expectedResponse);

    $result = $this->resource->find($customerId);

    expect($result)->toBe($expectedResponse);
});
