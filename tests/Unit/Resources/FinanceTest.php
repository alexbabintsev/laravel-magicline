<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Finance;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
    $this->resource = new Finance($this->client);
});

test('get debt collection configuration', function () {
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
});
