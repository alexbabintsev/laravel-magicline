<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CustomersCommunication;
use alexbabintsev\Magicline\Tests\TestCase;

class CustomersCommunicationTest extends TestCase
{
    protected CustomersCommunication $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new CustomersCommunication($this->client);
    }

    public function test_create_thread()
    {
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

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with("/v1/communications/{$customerId}/threads", $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createThread($customerId, $data);

        expect($result)->toBe($expectedResponse);
    }

    public function test_add_to_thread()
    {
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

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with("/v1/communications/{$customerId}/threads/{$threadId}", $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->addToThread($customerId, $threadId, $data);

        expect($result)->toBe($expectedResponse);
    }
}
