<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Appointments;
use Mockery;

beforeEach(function () {
    $this->client = Mockery::mock(MagiclineClient::class);
    $this->resource = new Appointments($this->client);
});

test('get bookable appointments', function () {
    $expectedResponse = ['data' => ['appointments']];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with('/v1/appointments/bookable', [])
        ->andReturn($expectedResponse);

    $result = $this->resource->getBookable();

    expect($result)->toBe($expectedResponse);
});

test('book appointment', function () {
    $data = ['appointmentId' => 123, 'customerId' => 456];
    $expectedResponse = ['success' => true];

    $this->client
        ->shouldReceive('post')
        ->once()
        ->with('/v1/appointments', $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->book($data);

    expect($result)->toBe($expectedResponse);
});

test('cancel appointment', function () {
    $appointmentId = 789;
    $expectedResponse = ['success' => true];

    $this->client
        ->shouldReceive('delete')
        ->once()
        ->with("/v1/appointments/{$appointmentId}")
        ->andReturn($expectedResponse);

    $result = $this->resource->cancel($appointmentId);

    expect($result)->toBe($expectedResponse);
});
