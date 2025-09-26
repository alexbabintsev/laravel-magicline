<?php

use AlexBabintsev\Magicline\Device\DTOs\Identification\WalletPassIdentification;

it('can create wallet pass identification', function () {
    $identification = WalletPassIdentification::create('123e4567-e89b-12d3-a456-426614174000');

    expect($identification->type)->toBe('WALLET_PASS');
    expect($identification->value)->toBe('123e4567-e89b-12d3-a456-426614174000');
});

it('can validate UUID format', function () {
    $validIdentification = WalletPassIdentification::create('123e4567-e89b-12d3-a456-426614174000');
    $invalidIdentification = WalletPassIdentification::create('invalid-uuid');

    expect($validIdentification->isValidUuid())->toBeTrue();
    expect($invalidIdentification->isValidUuid())->toBeFalse();
});

it('can get UUID version', function () {
    $uuid4 = WalletPassIdentification::create('123e4567-e89b-12d3-a456-426614174000');
    $invalidUuid = WalletPassIdentification::create('invalid-uuid');

    expect($uuid4->getUuidVersion())->toBe(1);
    expect($invalidUuid->getUuidVersion())->toBeNull();
});

it('can convert to api array', function () {
    $identification = WalletPassIdentification::create('123e4567-e89b-12d3-a456-426614174000');

    expect($identification->toApiArray())->toBe([
        'type' => 'WALLET_PASS',
        'value' => '123e4567-e89b-12d3-a456-426614174000',
    ]);
});
