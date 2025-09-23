<?php

namespace AlexBabintsev\Magicline\Tests\Unit\DataTransferObjects;

use AlexBabintsev\Magicline\DataTransferObjects\Appointment;

test('appointment can be created from array', function () {
    $data = [
        'id' => 123,
        'title' => 'Personal Training Session',
        'description' => 'One-on-one training with John',
        'startTime' => '2024-01-15T10:00:00Z',
        'endTime' => '2024-01-15T11:00:00Z',
        'status' => 'confirmed',
        'customerId' => 456,
        'employeeId' => 789,
        'type' => 'personal_training',
        'isBookable' => true,
    ];

    $appointment = Appointment::from($data);

    expect($appointment->id)->toBe(123)
        ->and($appointment->title)->toBe('Personal Training Session')
        ->and($appointment->customerId)->toBe(456)
        ->and($appointment->employeeId)->toBe(789)
        ->and($appointment->isBookable)->toBeTrue();
});

test('appointment to array', function () {
    $appointment = new Appointment([
        'id' => 456,
        'title' => 'Group Class',
        'status' => 'pending',
        'isBookable' => false,
    ]);

    $array = $appointment->toArray();

    expect($array['id'])->toBe(456)
        ->and($array['title'])->toBe('Group Class')
        ->and($array['status'])->toBe('pending')
        ->and($array['isBookable'])->toBeFalse();
});

test('appointment collection', function () {
    $data = [
        ['id' => 1, 'title' => 'Appointment 1', 'status' => 'confirmed'],
        ['id' => 2, 'title' => 'Appointment 2', 'status' => 'pending'],
    ];

    $appointments = Appointment::collection($data);

    expect($appointments)->toHaveCount(2)
        ->and($appointments[0])->toBeInstanceOf(Appointment::class)
        ->and($appointments[0]->title)->toBe('Appointment 1')
        ->and($appointments[1]->status)->toBe('pending');
});
