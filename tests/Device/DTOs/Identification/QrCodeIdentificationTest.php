<?php

use AlexBabintsev\Magicline\Device\DTOs\Identification\QrCodeIdentification;

it('can create QR code identification', function () {
    $identification = QrCodeIdentification::create('QR12345');

    expect($identification->type)->toBe('QR_CODE');
    expect($identification->value)->toBe('QR12345');
});

it('can convert to api array', function () {
    $identification = QrCodeIdentification::create('QR12345');

    expect($identification->toApiArray())->toBe([
        'type' => 'QR_CODE',
        'value' => 'QR12345',
    ]);
});