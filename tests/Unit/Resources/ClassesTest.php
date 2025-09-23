<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Classes;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new Classes($this->mockClient);
});

test('list classes', function () {
    $expectedResponse = ['data' => ['classes']];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/classes', [])
        ->andReturn($expectedResponse);

    $result = $this->resource->list();

    expect($result)->toBe($expectedResponse);
});

test('book class', function () {
    $classId = 123;
    $data = ['customerId' => 456];
    $expectedResponse = ['success' => true];

    $this->mockClient
        ->shouldReceive('post')
        ->once()
        ->with("/v1/classes/{$classId}/book", $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->book($classId, $data);

    expect($result)->toBe($expectedResponse);
});

test('cancel class booking', function () {
    $classId = 123;
    $bookingId = 789;
    $expectedResponse = ['success' => true];

    $this->mockClient
        ->shouldReceive('delete')
        ->once()
        ->with("/v1/classes/{$classId}/bookings/{$bookingId}")
        ->andReturn($expectedResponse);

    $result = $this->resource->cancel($classId, $bookingId);

    expect($result)->toBe($expectedResponse);
});
