<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\MembershipsSelfService;
use Mockery;

beforeEach(function () {
    $this->client = Mockery::mock(MagiclineClient::class);
    $this->resource = new MembershipsSelfService($this->client);
});

test('get contract data', function () {
    $customerId = 123;
    $expectedResponse = [
        'data' => [
            'customerId' => 123,
            'contractId' => 'contract-456',
            'startDate' => '2023-01-01',
            'endDate' => '2024-01-01',
            'status' => 'active',
            'membershipType' => 'premium',
        ],
    ];

    $this->client
        ->shouldReceive('get')
        ->once()
        ->with("/v1/memberships/{$customerId}/self-service/contract-data")
        ->andReturn($expectedResponse);

    $result = $this->resource->getContractData($customerId);

    expect($result)->toBe($expectedResponse);
});

test('cancel ordinary contract', function () {
    $customerId = 123;
    $data = [
        'reason' => 'Moving to another city',
        'endDate' => '2024-03-31',
    ];

    $expectedResponse = [
        'success' => true,
        'cancellationId' => 'cancel-789',
        'effectiveDate' => '2024-03-31',
    ];

    $this->client
        ->shouldReceive('post')
        ->once()
        ->with("/v1/memberships/{$customerId}/self-service/ordinary-contract-cancelation", $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->cancelOrdinaryContract($customerId, $data);

    expect($result)->toBe($expectedResponse);
});
