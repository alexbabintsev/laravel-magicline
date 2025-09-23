<?php

use alexbabintsev\Magicline\DataTransferObjects\Employee;

test('employee can be created from array', function () {
    $data = [
        'id' => 123,
        'firstName' => 'John',
        'lastName' => 'Trainer',
        'email' => 'john@gym.com',
        'phone' => '+49123456789',
        'position' => 'Personal Trainer',
        'department' => 'Fitness',
        'hireDate' => '2023-01-15',
        'isActive' => true,
    ];

    $employee = Employee::from($data);

    expect($employee->id)->toBe(123);
    expect($employee->firstName)->toBe('John');
    expect($employee->lastName)->toBe('Trainer');
    expect($employee->email)->toBe('john@gym.com');
    expect($employee->position)->toBe('Personal Trainer');
    expect($employee->isActive)->toBeTrue();
});

test('employee to array', function () {
    $employee = new Employee([
        'id' => 456,
        'firstName' => 'Sarah',
        'lastName' => 'Manager',
        'position' => 'Gym Manager',
    ]);

    $array = $employee->toArray();

    expect($array['id'])->toBe(456);
    expect($array['firstName'])->toBe('Sarah');
    expect($array['lastName'])->toBe('Manager');
    expect($array['position'])->toBe('Gym Manager');
});

test('employee collection', function () {
    $data = [
        ['id' => 1, 'firstName' => 'John', 'position' => 'Trainer'],
        ['id' => 2, 'firstName' => 'Sarah', 'position' => 'Manager'],
    ];

    $employees = Employee::collection($data);

    expect($employees)->toHaveCount(2);
    expect($employees[0])->toBeInstanceOf(Employee::class);
    expect($employees[0]->firstName)->toBe('John');
    expect($employees[1]->position)->toBe('Manager');
});
