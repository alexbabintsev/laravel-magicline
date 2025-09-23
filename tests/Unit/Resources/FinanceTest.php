<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Finance;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new Finance($this->mockClient);
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

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/debt-collection/configuration')
        ->andReturn($expectedResponse);

    $result = $this->resource->getDebtCollectionConfiguration();

    expect($result)->toBe($expectedResponse);
});
