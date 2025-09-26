<?php

use AlexBabintsev\Magicline\Device\DTOs\Identification\CardNumberIdentification;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingSaleRequest;

it('can create vending sale request', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);

    expect($request->identification->value)->toBe($identification->value);
    expect($request->identification->type)->toBe($identification->type);
    expect($request->transactionId)->toBe('txn-123');
    expect($request->productId)->toBe('shelf-1');
    expect($request->price)->toBe(2.50);
    expect($request->shouldExecuteAction)->toBeTrue();
});

it('can create dry run sale request', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::dryRun($identification, 'txn-123', 'shelf-1', 2.50);

    expect($request->shouldExecuteAction)->toBeFalse();
    expect($request->isDryRun())->toBeTrue();
});

it('can check if request is dry run', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $normalRequest = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);
    $dryRunRequest = VendingSaleRequest::dryRun($identification, 'txn-123', 'shelf-1', 2.50);

    expect($normalRequest->isDryRun())->toBeFalse();
    expect($dryRunRequest->isDryRun())->toBeTrue();
});

it('can get identification', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);

    $retrievedIdentification = $request->getIdentification();
    expect($retrievedIdentification->value)->toBe($identification->value);
    expect($retrievedIdentification->type)->toBe($identification->type);
});

it('can get transaction ID', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);

    expect($request->getTransactionId())->toBe('txn-123');
});

it('can get product ID', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);

    expect($request->getProductId())->toBe('shelf-1');
});

it('can get price', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);

    expect($request->getPrice())->toBe(2.50);
});

it('can get formatted price', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);

    expect($request->getFormattedPrice())->toBe('2.50 EUR');
    expect($request->getFormattedPrice('USD'))->toBe('2.50 USD');
});

it('can convert to API payload', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50, false);

    expect($request->toApiPayload())->toBe([
        'identification' => [
            'type' => 'NUMBER',
            'value' => '1234567890',
            'format' => 'DECIMAL',
        ],
        'transactionId' => 'txn-123',
        'productId' => 'shelf-1',
        'price' => 2.50,
        'shouldExecuteAction' => false,
    ]);
});
