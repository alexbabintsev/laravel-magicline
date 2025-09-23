<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Employees;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new Employees($this->mockClient);
});

test('list without pagination', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 1, 'firstName' => 'John', 'lastName' => 'Smith'],
            ['id' => 2, 'firstName' => 'Jane', 'lastName' => 'Doe'],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/employees', [])
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

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/employees', ['offset' => '10', 'sliceSize' => 25])
        ->andReturn($expectedResponse);

    $result = $this->resource->list(10, 25);

    expect($result)->toBe($expectedResponse);
});
