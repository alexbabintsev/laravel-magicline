<?php

use AlexBabintsev\Magicline\Device\DTOs\Access\CardReaderIdentificationRequest;
use AlexBabintsev\Magicline\Device\DTOs\Access\CardReaderIdentificationResponse;
use AlexBabintsev\Magicline\Device\DTOs\Identification\CardNumberIdentification;
use AlexBabintsev\Magicline\Device\Http\MagiclineDeviceClient;
use AlexBabintsev\Magicline\Device\Resources\AccessResource;

beforeEach(function () {
    $this->client = mock(MagiclineDeviceClient::class);
    $this->accessResource = new AccessResource($this->client);
});

it('can perform card reader identification', function () {
    $identification = CardNumberIdentification::decimal('1234567890');
    $request = CardReaderIdentificationRequest::create($identification);

    $this->client
        ->shouldReceive('post')
        ->once()
        ->with('access/card-reader/identification', [
            'identification' => [
                'type' => 'NUMBER',
                'value' => '1234567890',
                'format' => 'DECIMAL',
            ],
            'shouldExecuteAction' => true,
        ])
        ->andReturn([
            'text' => 'Access granted',
            'success' => true,
        ]);

    $response = $this->accessResource->cardReaderIdentification($request);

    expect($response)->toBeInstanceOf(CardReaderIdentificationResponse::class);
    expect($response->isSuccess())->toBeTrue();
    expect($response->getText())->toBe('Access granted');
});