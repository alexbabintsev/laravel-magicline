<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CustomersAccount;
use Mockery;

beforeEach(function () {
    $this->client = Mockery::mock(MagiclineClient::class);
    $this->resource = new CustomersAccount($this->client);
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

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/v1/customers/{$customerId}/account/balances")
        ->andReturn($expectedResponse);

    $result = $this->resource->getBalances($customerId);

    expect($result)->toBe($expectedResponse);
});
