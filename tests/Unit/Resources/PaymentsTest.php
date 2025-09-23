<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Payments;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new Payments($this->mockClient);
});

test('create user session', function () {
    $data = [
        'customerId' => 123,
        'amount' => 29.99,
        'currency' => 'EUR',
    ];
    $expectedResponse = [
        'sessionId' => 'session_123',
        'url' => 'https://payment.example.com/session_123',
    ];

    $this->mockClient
        ->shouldReceive('post')
        ->once()
        ->with('/v1/payments/user-session', $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->createUserSession($data);

    expect($result)->toBe($expectedResponse);
});