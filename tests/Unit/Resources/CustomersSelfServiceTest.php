<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CustomersSelfService;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
    $this->resource = new CustomersSelfService($this->client);
});

test('get contact data', function () {
    $customerId = 123;
    $expectedResponse = [
        'data' => [
            'id' => 123,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+49123456789',
        ],
    ];

    $this->client
        ->expects($this->once())
        ->method('get')
        ->with("/v1/customers/{$customerId}/self-service/contact-data")
        ->willReturn($expectedResponse);

    $result = $this->resource->getContactData($customerId);

    expect($result)->toBe($expectedResponse);
});

test('create contact data amendment', function () {
    $customerId = 123;
    $data = [
        'email' => 'newemail@example.com',
        'phone' => '+49987654321',
    ];

    $expectedResponse = [
        'success' => true,
        'message' => 'Contact data amendment created',
    ];

    $this->client
        ->expects($this->once())
        ->method('post')
        ->with("/v1/customers/{$customerId}/self-service/contact-data", $data)
        ->willReturn($expectedResponse);

    $result = $this->resource->createContactDataAmendment($customerId, $data);

    expect($result)->toBe($expectedResponse);
});
