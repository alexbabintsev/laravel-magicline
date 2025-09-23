<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Classes;
use alexbabintsev\Magicline\Tests\TestCase;

class ClassesTest extends TestCase
{
    protected Classes $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new Classes($this->client);
    }

    public function test_list_classes()
    {
        $expectedResponse = ['data' => ['classes']];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/classes', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        expect($result)->toBe($expectedResponse);
    }

    public function test_book_class()
    {
        $classId = 123;
        $data = ['customerId' => 456];
        $expectedResponse = ['success' => true];

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with("/v1/classes/{$classId}/book", $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->book($classId, $data);

        expect($result)->toBe($expectedResponse);
    }

    public function test_cancel_class_booking()
    {
        $classId = 123;
        $bookingId = 789;
        $expectedResponse = ['success' => true];

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->with("/v1/classes/{$classId}/bookings/{$bookingId}")
            ->willReturn($expectedResponse);

        $result = $this->resource->cancel($classId, $bookingId);

        expect($result)->toBe($expectedResponse);
    }
}
