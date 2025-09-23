<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CrossStudio;
use alexbabintsev\Magicline\Tests\TestCase;

class CrossStudioTest extends TestCase
{
    protected CrossStudio $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new CrossStudio($this->client);
    }

    public function test_get_customers_by()
    {
        $criteria = [
            'email' => 'test@example.com',
            'phone' => '+49123456789',
        ];

        $expectedResponse = [
            'data' => [
                ['id' => 1, 'firstName' => 'John', 'email' => 'test@example.com'],
                ['id' => 2, 'firstName' => 'Jane', 'phone' => '+49123456789'],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/cross-studio/customers/by', $criteria)
            ->willReturn($expectedResponse);

        $result = $this->resource->getCustomersBy($criteria);

        expect($result)->toBe($expectedResponse);
    }
}
