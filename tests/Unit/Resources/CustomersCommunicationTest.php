<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\CustomersCommunication;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new CustomersCommunication($this->mockClient);
});

test('create thread', function () {
    $customerId = 123;
    $data = [
        'subject' => 'Support Request',
        'message' => 'I need help with my membership',
        'priority' => 'normal',
    ];

    $expectedResponse = [
        'data' => [
            'threadId' => 'thread-456',
            'customerId' => 123,
            'subject' => 'Support Request',
            'status' => 'open',
        ],
    ];

    $this->mockClient
        ->shouldReceive('post')
        ->once()
        ->with("/v1/communications/{$customerId}/threads", $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->createThread($customerId, $data);

    expect($result)->toBe($expectedResponse);
});

test('add to thread', function () {
    $customerId = 123;
    $threadId = 'thread-456';
    $data = [
        'message' => 'Thank you for the quick response!',
        'sender' => 'customer',
    ];

    $expectedResponse = [
        'success' => true,
        'messageId' => 'msg-789',
    ];

    $this->mockClient
        ->shouldReceive('post')
        ->once()
        ->with("/v1/communications/{$customerId}/threads/{$threadId}", $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->addToThread($customerId, $threadId, $data);

    expect($result)->toBe($expectedResponse);
});
