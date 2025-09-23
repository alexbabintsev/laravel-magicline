<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Appointments;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
    $this->resource = new Appointments($this->client);
});

test('get bookable appointments', function () {
    $expectedResponse = ['data' => ['appointments']];

    $this->client
        ->expects($this->once())
        ->method('get')
        ->with('/v1/appointments/bookable', [])
        ->willReturn($expectedResponse);

    $result = $this->resource->getBookable();

    expect($result)->toBe($expectedResponse);
});

test('book appointment', function () {
    $data = ['appointmentId' => 123, 'customerId' => 456];
    $expectedResponse = ['success' => true];

    $this->client
        ->expects($this->once())
        ->method('post')
        ->with('/v1/appointments', $data)
        ->willReturn($expectedResponse);

    $result = $this->resource->book($data);

    expect($result)->toBe($expectedResponse);
});

test('cancel appointment', function () {
    $appointmentId = 789;
    $expectedResponse = ['success' => true];

    $this->client
        ->expects($this->once())
        ->method('delete')
        ->with("/v1/appointments/{$appointmentId}")
        ->willReturn($expectedResponse);

    $result = $this->resource->cancel($appointmentId);

    expect($result)->toBe($expectedResponse);
});
