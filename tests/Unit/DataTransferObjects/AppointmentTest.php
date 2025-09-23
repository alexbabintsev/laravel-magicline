<?php

namespace alexbabintsev\Magicline\Tests\Unit\DataTransferObjects;

use alexbabintsev\Magicline\DataTransferObjects\Appointment;
use alexbabintsev\Magicline\Tests\TestCase;

class AppointmentTest extends TestCase
{
    public function test_appointment_can_be_created_from_array()
    {
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

        expect($appointment->id)->toBe(123);
        expect($appointment->title)->toBe('Personal Training Session');
        expect($appointment->customerId)->toBe(456);
        expect($appointment->employeeId)->toBe(789);
        expect($appointment->isBookable)->toBeTrue();
    }

    public function test_appointment_to_array()
    {
        $appointment = new Appointment([
            'id' => 456,
            'title' => 'Group Class',
            'status' => 'pending',
            'isBookable' => false,
        ]);

        $array = $appointment->toArray();

        expect($array['id'])->toBe(456);
        expect($array['title'])->toBe('Group Class');
        expect($array['status'])->toBe('pending');
        expect($array['isBookable'])->toBeFalse();
    }

    public function test_appointment_collection()
    {
        $data = [
            ['id' => 1, 'title' => 'Appointment 1', 'status' => 'confirmed'],
            ['id' => 2, 'title' => 'Appointment 2', 'status' => 'pending'],
        ];

        $appointments = Appointment::collection($data);

        expect($appointments)->toHaveCount(2);
        expect($appointments[0])->toBeInstanceOf(Appointment::class);
        expect($appointments[0]->title)->toBe('Appointment 1');
        expect($appointments[1]->status)->toBe('pending');
    }
}
