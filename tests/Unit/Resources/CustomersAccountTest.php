<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CustomersAccount;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
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
        ->expects($this->once())
        ->method('get')
        ->with("/v1/customers/{$customerId}/account/balances")
        ->willReturn($expectedResponse);

    $result = $this->resource->getBalances($customerId);

    expect($result)->toBe($expectedResponse);
});
