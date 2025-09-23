<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Appointments;
use alexbabintsev\Magicline\Tests\TestCase;

class AppointmentsTest extends TestCase
{
    protected Appointments $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new Appointments($this->client);
    }

    public function test_get_bookable_appointments()
    {
        $expectedResponse = ['data' => ['appointments']];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/appointments/bookable', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getBookable();

        expect($result)->toBe($expectedResponse);
    }

    public function test_book_appointment()
    {
        $data = ['appointmentId' => 123, 'customerId' => 456];
        $expectedResponse = ['success' => true];

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with('/v1/appointments', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->book($data);

        expect($result)->toBe($expectedResponse);
    }

    public function test_cancel_appointment()
    {
        $appointmentId = 789;
        $expectedResponse = ['success' => true];

        $this->client
            ->expects($this->once())
            ->method('delete')
            ->with("/v1/appointments/{$appointmentId}")
            ->willReturn($expectedResponse);

        $result = $this->resource->cancel($appointmentId);

        expect($result)->toBe($expectedResponse);
    }
}
