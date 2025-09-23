<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\CustomersAccount;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new CustomersAccount($this->mockClient);
});

test('get balances', function () {
    $customerId = 123;
    $expectedResponse = [
        'data' => [
            'customerId' => 123,
            'balances' => [
                'creditBalance' => 50.00,
                'pointsBalance' => 150,
                'voucherBalance' => 2,
            ],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with("/v1/customers/{$customerId}/account/balances")
        ->andReturn($expectedResponse);

    $result = $this->resource->getBalances($customerId);

    expect($result)->toBe($expectedResponse);
});
