<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Customers;
use alexbabintsev\Magicline\Tests\TestCase;

class CustomersTest extends TestCase
{
    protected Customers $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new Customers($this->client);
    }

    public function test_list_without_pagination()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 1, 'firstName' => 'John', 'lastName' => 'Doe'],
                ['id' => 2, 'firstName' => 'Jane', 'lastName' => 'Smith'],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/customers', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        expect($result)->toBe($expectedResponse);
    }

    public function test_list_with_pagination()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 3, 'firstName' => 'Bob', 'lastName' => 'Johnson'],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/customers', ['offset' => '10', 'sliceSize' => 25])
            ->willReturn($expectedResponse);

        $result = $this->resource->list(10, 25);

        expect($result)->toBe($expectedResponse);
    }

    public function test_find()
    {
        $customerId = 123;
        $expectedResponse = [
            'data' => [
                'id' => 123,
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@example.com',
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with("/v1/customers/{$customerId}")
            ->willReturn($expectedResponse);

        $result = $this->resource->find($customerId);

        expect($result)->toBe($expectedResponse);
    }
}
