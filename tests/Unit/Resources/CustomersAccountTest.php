<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CustomersAccount;
use alexbabintsev\Magicline\Tests\TestCase;

class CustomersAccountTest extends TestCase
{
    protected CustomersAccount $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new CustomersAccount($this->client);
    }

    public function test_get_balances()
    {
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
    }
}
