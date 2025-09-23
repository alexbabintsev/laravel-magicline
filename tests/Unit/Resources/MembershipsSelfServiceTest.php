<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\MembershipsSelfService;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
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
        ->expects($this->once())
        ->method('get')
        ->with("/v1/memberships/{$customerId}/self-service/contract-data")
        ->willReturn($expectedResponse);

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
        ->expects($this->once())
        ->method('post')
        ->with("/v1/memberships/{$customerId}/self-service/ordinary-contract-cancelation", $data)
        ->willReturn($expectedResponse);

    $result = $this->resource->cancelOrdinaryContract($customerId, $data);

    expect($result)->toBe($expectedResponse);
});
