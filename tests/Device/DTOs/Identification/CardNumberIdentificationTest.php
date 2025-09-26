<?php

use AlexBabintsev\Magicline\Device\DTOs\Identification\CardNumberIdentification;

it('can create decimal card number identification', function () {
    $identification = CardNumberIdentification::decimal('1234567890');

    expect($identification->type)->toBe('NUMBER');
    expect($identification->value)->toBe('1234567890');
    expect($identification->format)->toBe('DECIMAL');
});

it('can create hex MSB card number identification', function () {
    $identification = CardNumberIdentification::hexMsb('1A2B3C4D');

    expect($identification->type)->toBe('NUMBER');
    expect($identification->value)->toBe('1A2B3C4D');
    expect($identification->format)->toBe('HEX_MSB');
});

it('can create hex LSB card number identification', function () {
    $identification = CardNumberIdentification::hexLsb('4D3C2B1A');

    expect($identification->type)->toBe('NUMBER');
    expect($identification->value)->toBe('4D3C2B1A');
    expect($identification->format)->toBe('HEX_LSB');
});

it('can validate decimal format', function () {
    $identification = CardNumberIdentification::decimal('1234567890');

    expect($identification->isValidDecimal())->toBeTrue();
});

it('can detect invalid decimal format', function () {
    $identification = CardNumberIdentification::decimal('123ABC789');

    expect($identification->isValidDecimal())->toBeFalse();
});

it('can validate hex format', function () {
    $identification = CardNumberIdentification::hexMsb('1A2B3C4D');

    expect($identification->isValidHex())->toBeTrue();
});

it('can detect invalid hex format', function () {
    $identification = CardNumberIdentification::hexMsb('1G2H3I4J');

    expect($identification->isValidHex())->toBeFalse();
});

it('can convert to api array', function () {
    $identification = CardNumberIdentification::decimal('1234567890');

    expect($identification->toApiArray())->toBe([
        'type' => 'NUMBER',
        'value' => '1234567890',
        'format' => 'DECIMAL',
    ]);
});
