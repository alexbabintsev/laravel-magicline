<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Classes;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
    $this->resource = new Classes($this->client);
});

test('list classes', function () {
    $expectedResponse = ['data' => ['classes']];

    $this->client
        ->expects($this->once())
        ->method('get')
        ->with('/v1/classes', [])
        ->willReturn($expectedResponse);

    $result = $this->resource->list();

    expect($result)->toBe($expectedResponse);
});

test('book class', function () {
    $classId = 123;
    $data = ['customerId' => 456];
    $expectedResponse = ['success' => true];

    $this->client
        ->expects($this->once())
        ->method('post')
        ->with("/v1/classes/{$classId}/book", $data)
        ->willReturn($expectedResponse);

    $result = $this->resource->book($classId, $data);

    expect($result)->toBe($expectedResponse);
});

test('cancel class booking', function () {
    $classId = 123;
    $bookingId = 789;
    $expectedResponse = ['success' => true];

    $this->client
        ->expects($this->once())
        ->method('delete')
        ->with("/v1/classes/{$classId}/bookings/{$bookingId}")
        ->willReturn($expectedResponse);

    $result = $this->resource->cancel($classId, $bookingId);

    expect($result)->toBe($expectedResponse);
});
