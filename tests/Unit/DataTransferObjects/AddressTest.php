<?php

namespace alexbabintsev\Magicline\Tests\Unit\DataTransferObjects;

use alexbabintsev\Magicline\DataTransferObjects\Address;
test('address can be created from array', function () {
    $data = [
        'street' => 'Main Street 123',
        'city' => 'Berlin',
        'postalCode' => '10115',
        'country' => 'Germany',
        'state' => 'Berlin',
    ];

    $address = Address::from($data);

    expect($address->street)->toBe('Main Street 123');
    expect($address->city)->toBe('Berlin');
    expect($address->postalCode)->toBe('10115');
    expect($address->country)->toBe('Germany');
    expect($address->state)->toBe('Berlin');
});

test('address to array', function () {
    $address = new Address([
        'street' => 'Test Street 456',
        'city' => 'Munich',
        'postalCode' => '80331',
        'country' => 'Germany',
    ]);

    $array = $address->toArray();

    expect($array['street'])->toBe('Test Street 456');
    expect($array['city'])->toBe('Munich');
    expect($array['postalCode'])->toBe('80331');
    expect($array['country'])->toBe('Germany');
    expect($array['state'])->toBeNull();
});

test('address collection', function () {
    $data = [
        ['street' => 'Street 1', 'city' => 'City 1'],
        ['street' => 'Street 2', 'city' => 'City 2'],
    ];

    $addresses = Address::collection($data);

    expect($addresses)->toHaveCount(2);
    expect($addresses[0])->toBeInstanceOf(Address::class);
    expect($addresses[0]->street)->toBe('Street 1');
    expect($addresses[1]->city)->toBe('City 2');
});

test('address with partial data', function () {
    $data = ['city' => 'Hamburg', 'country' => 'Germany'];

    $address = Address::from($data);

    expect($address->street)->toBeNull();
    expect($address->city)->toBe('Hamburg');
    expect($address->postalCode)->toBeNull();
    expect($address->country)->toBe('Germany');
});
