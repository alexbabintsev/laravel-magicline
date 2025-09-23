<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Memberships;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new Memberships($this->mockClient);
});

test('get membership offers', function () {
    $expectedResponse = [
        'data' => [
            ['id' => 1, 'name' => 'Basic Membership', 'price' => 29.99],
            ['id' => 2, 'name' => 'Premium Membership', 'price' => 49.99],
        ],
    ];

    $this->mockClient
        ->shouldReceive('get')
        ->once()
        ->with('/v1/memberships/membership-offers')
        ->andReturn($expectedResponse);

    $result = $this->resource->getOffers();

    expect($result)->toBe($expectedResponse);
});
