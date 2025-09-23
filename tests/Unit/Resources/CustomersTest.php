<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Customers;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
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
        ->expects($this->once())
        ->method('get')
        ->with('/v1/customers', [])
        ->willReturn($expectedResponse);

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
        ->expects($this->once())
        ->method('get')
        ->with('/v1/customers', ['offset' => '10', 'sliceSize' => 25])
        ->willReturn($expectedResponse);

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
        ->expects($this->once())
        ->method('get')
        ->with("/v1/customers/{$customerId}")
        ->willReturn($expectedResponse);

    $result = $this->resource->find($customerId);

    expect($result)->toBe($expectedResponse);
});
