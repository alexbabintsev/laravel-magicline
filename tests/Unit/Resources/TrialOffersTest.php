<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\TrialOffers;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new TrialOffers($this->mockClient);
});

test('get bookable classes without pagination', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 1, 'name' => 'Trial Yoga Class', 'date' => '2024-01-15'],
            ['id' => 2, 'name' => 'Trial Fitness Class', 'date' => '2024-01-16'],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/trial-offers/bookable-trial-offers/classes', [])
        ->andReturn($expectedResponse);

    $result = $this->resource->getBookableClasses();

    expect($result)->toBe($expectedResponse);
});

test('get bookable classes with pagination', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 3, 'name' => 'Trial Pilates Class', 'date' => '2024-01-17'],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/trial-offers/bookable-trial-offers/classes', ['offset' => '10', 'sliceSize' => 25])
        ->andReturn($expectedResponse);

    $result = $this->resource->getBookableClasses(10, 25);

    expect($result)->toBe($expectedResponse);
});

test('get bookable appointments without pagination', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 1, 'title' => 'Trial Personal Training', 'date' => '2024-01-15'],
            ['id' => 2, 'title' => 'Trial Consultation', 'date' => '2024-01-16'],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/trial-offers/bookable-trial-offers/appointments/bookable', [])
        ->andReturn($expectedResponse);

    $result = $this->resource->getBookableAppointments();

    expect($result)->toBe($expectedResponse);
});

test('get bookable appointments with pagination', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 3, 'title' => 'Trial Nutrition Consultation', 'date' => '2024-01-17'],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/trial-offers/bookable-trial-offers/appointments/bookable', ['offset' => '20', 'sliceSize' => 50])
        ->andReturn($expectedResponse);

    $result = $this->resource->getBookableAppointments(20, 50);

    expect($result)->toBe($expectedResponse);
});
