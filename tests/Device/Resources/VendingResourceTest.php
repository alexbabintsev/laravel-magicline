<?php

use AlexBabintsev\Magicline\Device\DTOs\Identification\CardNumberIdentification;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingIdentificationRequest;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingIdentificationResponse;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingSaleRequest;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingSaleResponse;
use AlexBabintsev\Magicline\Device\Http\MagiclineDeviceClient;
use AlexBabintsev\Magicline\Device\Resources\VendingResource;

beforeEach(function () {
    $this->client = mock(MagiclineDeviceClient::class);
    $this->vendingResource = new VendingResource($this->client);
});

it('can perform vending identification', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingIdentificationRequest::create($identification, 'txn-123');

    $this->client
        ->shouldReceive('post')
        ->once()
        ->with('vending/identification', [
            'identification' => [
                'type' => 'NUMBER',
                'value' => '1234567890',
                'format' => 'DECIMAL',
            ],
            'transactionId' => 'txn-123',
        ])
        ->andReturn([
            'text' => 'Customer authorized',
            'authorized' => true,
            'consumptionCredit' => 50.00,
            'transactionId' => 'txn-123',
        ]);

    $response = $this->vendingResource->identification($request);

    expect($response)->toBeInstanceOf(VendingIdentificationResponse::class);
    expect($response->isAuthorized())->toBeTrue();
    expect($response->getText())->toBe('Customer authorized');
    expect($response->getConsumptionCredit())->toBe(50.00);
    expect($response->getTransactionId())->toBe('txn-123');
});

it('can perform vending sale', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = VendingSaleRequest::create($identification, 'txn-123', 'shelf-1', 2.50);

    $this->client
        ->shouldReceive('post')
        ->once()
        ->with('vending/sale', [
            'identification' => [
                'type' => 'NUMBER',
                'value' => '1234567890',
                'format' => 'DECIMAL',
            ],
            'transactionId' => 'txn-123',
            'productId' => 'shelf-1',
            'price' => 2.50,
            'shouldExecuteAction' => true,
        ])
        ->andReturn([
            'text' => 'Sale completed',
            'success' => true,
            'transactionId' => 'txn-123',
        ]);

    $response = $this->vendingResource->sale($request);

    expect($response)->toBeInstanceOf(VendingSaleResponse::class);
    expect($response->isSuccess())->toBeTrue();
    expect($response->getText())->toBe('Sale completed');
    expect($response->getTransactionId())->toBe('txn-123');
});
