<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\CustomersSelfService;
use Mockery;

beforeEach(function () {
    $this->client = Mockery::mock(MagiclineClient::class);
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
        ->shouldReceive('get')
        ->once()
        ->with("/v1/customers/{$customerId}/self-service/contact-data")
        ->andReturn($expectedResponse);

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
        ->shouldReceive('post')
        ->once()
        ->with("/v1/customers/{$customerId}/self-service/contact-data", $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->createContactDataAmendment($customerId, $data);

    expect($result)->toBe($expectedResponse);
});
