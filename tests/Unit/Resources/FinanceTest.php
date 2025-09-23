<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Finance;
use alexbabintsev\Magicline\Tests\TestCase;

class FinanceTest extends TestCase
{
    protected Finance $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new Finance($this->client);
    }

    public function test_get_debt_collection_configuration()
    {
        $expectedResponse = [
            'data' => [
                'enabled' => true,
                'warningDays' => 7,
                'reminderDays' => 14,
                'collectionDays' => 30,
                'fees' => [
                    'reminder' => 5.00,
                    'collection' => 25.00,
                ],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/debt-collection/configuration')
            ->willReturn($expectedResponse);

        $result = $this->resource->getDebtCollectionConfiguration();

        expect($result)->toBe($expectedResponse);
    }
}
