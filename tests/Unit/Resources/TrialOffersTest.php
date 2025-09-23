<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\TrialOffers;
use alexbabintsev\Magicline\Tests\TestCase;

class TrialOffersTest extends TestCase
{
    protected TrialOffers $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new TrialOffers($this->client);
    }

    public function test_get_bookable_classes_without_pagination()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 1, 'name' => 'Trial Yoga Class', 'date' => '2024-01-15'],
                ['id' => 2, 'name' => 'Trial Fitness Class', 'date' => '2024-01-16'],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/trial-offers/bookable-trial-offers/classes', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getBookableClasses();

        expect($result)->toBe($expectedResponse);
    }

    public function test_get_bookable_classes_with_pagination()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 3, 'name' => 'Trial Pilates Class', 'date' => '2024-01-17'],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/trial-offers/bookable-trial-offers/classes', ['offset' => '10', 'sliceSize' => 25])
            ->willReturn($expectedResponse);

        $result = $this->resource->getBookableClasses(10, 25);

        expect($result)->toBe($expectedResponse);
    }

    public function test_get_bookable_appointments_without_pagination()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 1, 'title' => 'Trial Personal Training', 'date' => '2024-01-15'],
                ['id' => 2, 'title' => 'Trial Consultation', 'date' => '2024-01-16'],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/trial-offers/bookable-trial-offers/appointments/bookable', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getBookableAppointments();

        expect($result)->toBe($expectedResponse);
    }

    public function test_get_bookable_appointments_with_pagination()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 3, 'title' => 'Trial Nutrition Consultation', 'date' => '2024-01-17'],
            ],
        ];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with('/v1/trial-offers/bookable-trial-offers/appointments/bookable', ['offset' => '20', 'sliceSize' => 50])
            ->willReturn($expectedResponse);

        $result = $this->resource->getBookableAppointments(20, 50);

        expect($result)->toBe($expectedResponse);
    }
}
