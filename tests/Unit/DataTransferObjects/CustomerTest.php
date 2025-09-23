<?php

use alexbabintsev\Magicline\DataTransferObjects\Address;
use alexbabintsev\Magicline\DataTransferObjects\Customer;

test('customer can be created from array', function () {
    $data = [
        'id' => 123,
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Anytown',
            'postalCode' => '12345',
            'country' => 'USA',
        ],
    ];

    $customer = Customer::from($data);

    expect($customer->id)->toBe(123)
        ->and($customer->firstName)->toBe('John')
        ->and($customer->lastName)->toBe('Doe')
        ->and($customer->email)->toBe('john@example.com')
        ->and($customer->phone)->toBe('+1234567890')
        ->and($customer->address)->toBeInstanceOf(Address::class)
        ->and($customer->address->street)->toBe('123 Main St')
        ->and($customer->address->city)->toBe('Anytown');
});

test('customer to array', function () {
    $customer = new Customer([
        'id' => 123,
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $array = $customer->toArray();

    expect($array['id'])->toBe(123)
        ->and($array['firstName'])->toBe('John')
        ->and($array['lastName'])->toBe('Doe')
        ->and($array['email'])->toBe('john@example.com');
});

test('customer collection', function () {
    $data = [
        ['id' => 1, 'firstName' => 'John', 'lastName' => 'Doe'],
        ['id' => 2, 'firstName' => 'Jane', 'lastName' => 'Smith'],
    ];

    $customers = Customer::collection($data);

    expect($customers)->toHaveCount(2)
        ->and($customers[0])->toBeInstanceOf(Customer::class)
        ->and($customers[0]->firstName)->toBe('John')
        ->and($customers[1]->firstName)->toBe('Jane');
});
