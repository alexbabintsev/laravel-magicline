<?php

namespace AlexBabintsev\Magicline\Tests\Unit\DataTransferObjects;

use AlexBabintsev\Magicline\DataTransferObjects\Address;

test('address can be created from array', function () {
    $data = [
        'street' => 'Main Street 123',
        'city' => 'Berlin',
        'postalCode' => '10115',
        'country' => 'Germany',
        'state' => 'Berlin',
    ];

    $address = Address::from($data);

    expect($address->street)->toBe('Main Street 123')
        ->and($address->city)->toBe('Berlin')
        ->and($address->postalCode)->toBe('10115')
        ->and($address->country)->toBe('Germany')
        ->and($address->state)->toBe('Berlin');
});

test('address to array', function () {
    $address = new Address([
        'street' => 'Test Street 456',
        'city' => 'Munich',
        'postalCode' => '80331',
        'country' => 'Germany',
    ]);

    $array = $address->toArray();

    expect($array['street'])->toBe('Test Street 456')
        ->and($array['city'])->toBe('Munich')
        ->and($array['postalCode'])->toBe('80331')
        ->and($array['country'])->toBe('Germany')
        ->and($array['state'])->toBeNull();
});

test('address collection', function () {
    $data = [
        ['street' => 'Street 1', 'city' => 'City 1'],
        ['street' => 'Street 2', 'city' => 'City 2'],
    ];

    $addresses = Address::collection($data);

    expect($addresses)->toHaveCount(2)
        ->and($addresses[0])->toBeInstanceOf(Address::class)
        ->and($addresses[0]->street)->toBe('Street 1')
        ->and($addresses[1]->city)->toBe('City 2');
});

test('address with partial data', function () {
    $data = ['city' => 'Hamburg', 'country' => 'Germany'];

    $address = Address::from($data);

    expect($address->street)->toBeNull()
        ->and($address->city)->toBe('Hamburg')
        ->and($address->postalCode)->toBeNull()
        ->and($address->country)->toBe('Germany');
});
