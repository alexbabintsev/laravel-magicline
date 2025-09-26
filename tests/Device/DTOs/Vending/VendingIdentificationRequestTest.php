<?php

use AlexBabintsev\Magicline\Device\DTOs\Identification\CardNumberIdentification;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingIdentificationRequest;

it('can create vending identification request', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingIdentificationRequest::create($identification, 'txn-123');

    expect($request->identification->value)->toBe($identification->value);
    expect($request->identification->type)->toBe($identification->type);
    expect($request->transactionId)->toBe('txn-123');
});

it('can create vending identification request with generated UUID', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingIdentificationRequest::createWithGeneratedId($identification);

    expect($request->identification->value)->toBe($identification->value);
    expect($request->identification->type)->toBe($identification->type);
    expect($request->getTransactionId())->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('can generate transaction ID', function () {
    $transactionId = VendingIdentificationRequest::generateTransactionId();

    expect($transactionId)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('can get identification', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingIdentificationRequest::create($identification, 'txn-123');

    $retrievedIdentification = $request->getIdentification();
    expect($retrievedIdentification->value)->toBe($identification->value);
    expect($retrievedIdentification->type)->toBe($identification->type);
});

it('can get transaction ID', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingIdentificationRequest::create($identification, 'txn-123');

    expect($request->getTransactionId())->toBe('txn-123');
});

it('can convert to API payload', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingIdentificationRequest::create($identification, 'txn-123');

    expect($request->toApiPayload())->toBe([
        'identification' => [
            'type' => 'NUMBER',
            'value' => '1234567890',
            'format' => 'DECIMAL',
        ],
        'transactionId' => 'txn-123',
    ]);
});
