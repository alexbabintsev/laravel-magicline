<?php

use AlexBabintsev\Magicline\Webhooks\Http\Controllers\WebhookController;
use AlexBabintsev\Magicline\Webhooks\WebhookHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

beforeEach(function () {
    $this->mockHandler = Mockery::mock(WebhookHandler::class);
    $this->controller = new WebhookController($this->mockHandler);

    // For empty payload test, use real handler
    $this->realHandler = new WebhookHandler;
    $this->realController = new WebhookController($this->realHandler);
});

test('can handle valid webhook request', function () {
    $payload = [
        'entityId' => 123,
        'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'payload' => [
            [
                'timestamp' => 1640995200000,
                'type' => 'CUSTOMER_CREATED',
                'content' => ['customerId' => 456],
            ],
        ],
    ];

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($payload));
    $request->headers->set('Content-Type', 'application/json');

    $this->mockHandler->shouldReceive('validatePayload')
        ->once()
        ->with($payload)
        ->andReturn(true);

    $this->mockHandler->shouldReceive('handle')
        ->once();

    $this->mockHandler->shouldReceive('getProcessingStats')
        ->once()
        ->andReturn([
            'total_events' => 1,
            'entity_id' => 123,
            'request_uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'event_types' => ['CUSTOMER_CREATED'],
        ]);

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData)->toMatchArray([
        'message' => 'Webhook processed successfully',
        'processed' => 1,
        'entity_id' => 123,
        'request_uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'event_types' => ['CUSTOMER_CREATED'],
    ]);
});

test('rejects request with invalid payload structure', function () {
    $invalidPayload = [
        'entityId' => 'invalid', // Should be integer
        'uuid' => 'not-a-uuid',
        'payload' => 'not-an-array',
    ];

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($invalidPayload));
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData)->toHaveKey('error');
    expect($responseData['error'])->toBe('Invalid payload structure');
    expect($responseData)->toHaveKey('details');
});

test('rejects request when handler validation fails', function () {
    $payload = [
        'entityId' => 123,
        'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'payload' => [
            [
                'timestamp' => 1640995200000,
                'type' => 'CUSTOMER_CREATED',
            ],
        ],
    ];

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($payload));
    $request->headers->set('Content-Type', 'application/json');

    $this->mockHandler->shouldReceive('validatePayload')
        ->once()
        ->with($payload)
        ->andReturn(false);

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Invalid webhook payload');
});

test('handles empty payload with validation error', function () {
    $payload = [
        'entityId' => 123,
        'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'payload' => [],
    ];

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($payload));
    $request->headers->set('Content-Type', 'application/json');

    // Use real controller - empty payload passes Laravel validation but fails handler validation
    $response = $this->realController->handle($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Invalid payload structure');
});

test('handles webhook processing exceptions', function () {

    $payload = [
        'entityId' => 123,
        'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'payload' => [
            [
                'timestamp' => 1640995200000,
                'type' => 'CUSTOMER_CREATED',
                'content' => ['customerId' => 456],
            ],
        ],
    ];

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($payload));
    $request->headers->set('Content-Type', 'application/json');

    $this->mockHandler->shouldReceive('validatePayload')
        ->once()
        ->andReturn(true);

    $this->mockHandler->shouldReceive('handle')
        ->once()
        ->andThrow(new \AlexBabintsev\Magicline\Webhooks\Exceptions\WebhookProcessingException('Test error'));

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Webhook processing failed');

});

test('handles unexpected exceptions', function () {

    $payload = [
        'entityId' => 123,
        'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'payload' => [
            [
                'timestamp' => 1640995200000,
                'type' => 'CUSTOMER_CREATED',
                'content' => ['customerId' => 456],
            ],
        ],
    ];

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($payload));
    $request->headers->set('Content-Type', 'application/json');

    $this->mockHandler->shouldReceive('validatePayload')
        ->once()
        ->andThrow(new \Exception('Unexpected error'));

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Internal server error');

});

test('can return status endpoint', function () {
    $response = $this->controller->status();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData)->toMatchArray([
        'status' => 'active',
        'service' => 'Magicline Webhooks',
        'version' => '1.0.0',
    ]);
    expect($responseData)->toHaveKey('timestamp');
});

test('validates individual event structure', function () {
    $payloadWithInvalidEvent = [
        'entityId' => 123,
        'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'payload' => [
            [
                // Missing timestamp
                'type' => 'CUSTOMER_CREATED',
            ],
        ],
    ];

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($payloadWithInvalidEvent));
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Invalid payload structure');
    expect($responseData)->toHaveKey('details');
});

afterEach(function () {
    Mockery::close();
});
