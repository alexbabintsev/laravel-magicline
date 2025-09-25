<?php
use AlexBabintsev\Magicline\Webhooks\DTOs\WebhookEvent;
use AlexBabintsev\Magicline\Webhooks\DTOs\WebhookEventRequest;
use AlexBabintsev\Magicline\Webhooks\Exceptions\WebhookProcessingException;
use AlexBabintsev\Magicline\Webhooks\WebhookHandler;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->handler = new WebhookHandler();
});

    test('can validate webhook payload structure', function () {
        $validPayload = [
            'entityId' => 123,
            'uuid' => 'test-uuid',
            'payload' => [
                [
                    'timestamp' => 1640995200000,
                    'type' => 'CUSTOMER_CREATED',
                    'content' => ['customerId' => 456]
                ]
            ]
        ];

        expect($this->handler->validatePayload($validPayload))->toBeTrue();
    });

    test('rejects invalid webhook payload structure', function () {
        $invalidPayloads = [
            [],
            ['entityId' => 123],
            ['entityId' => 123, 'uuid' => 'test'],
            ['entityId' => 123, 'uuid' => 'test', 'payload' => 'not-array'],
        ];

        foreach ($invalidPayloads as $payload) {
            expect($this->handler->validatePayload($payload))->toBeFalse();
        }
    });

    test('can check if event type is supported', function () {
        expect($this->handler->isEventTypeSupported('CUSTOMER_CREATED'))->toBeTrue();
        expect($this->handler->isEventTypeSupported('CONTRACT_CREATED'))->toBeTrue();
        expect($this->handler->isEventTypeSupported('APPOINTMENT_BOOKING_CREATED'))->toBeTrue();
        expect($this->handler->isEventTypeSupported('EMPLOYEE_CREATED'))->toBeTrue();
        expect($this->handler->isEventTypeSupported('DEVICE_CREATED'))->toBeTrue();
        expect($this->handler->isEventTypeSupported('UNKNOWN_EVENT'))->toBeFalse();
    });

    test('can process webhook event request', function () {
        Event::fake();

        $webhookData = [
            'entityId' => 123,
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'payload' => [
                [
                    'timestamp' => 1640995200000,
                    'type' => 'CUSTOMER_CREATED',
                    'content' => ['customerId' => 456, 'firstName' => 'John']
                ]
            ]
        ];

        $request = WebhookEventRequest::from($webhookData);

        $this->handler->handle($request);

        Event::assertDispatched('webhook.received');
        Event::assertDispatched('webhook.CUSTOMER_CREATED');
        Event::assertDispatched('webhook.customer');
    });

    test('can process multiple events in single request', function () {
        Event::fake();

        $webhookData = [
            'entityId' => 123,
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'payload' => [
                [
                    'timestamp' => 1640995200000,
                    'type' => 'CUSTOMER_CREATED',
                    'content' => ['customerId' => 456]
                ],
                [
                    'timestamp' => 1640995260000,
                    'type' => 'CONTRACT_CREATED',
                    'content' => ['contractId' => 789]
                ]
            ]
        ];

        $request = WebhookEventRequest::from($webhookData);

        $this->handler->handle($request);

        Event::assertDispatched('webhook.received', 2);
        Event::assertDispatched('webhook.CUSTOMER_CREATED');
        Event::assertDispatched('webhook.CONTRACT_CREATED');
        Event::assertDispatched('webhook.customer');
        Event::assertDispatched('webhook.contract');
    });

    test('can get processing statistics', function () {
        $webhookData = [
            'entityId' => 123,
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'payload' => [
                [
                    'timestamp' => 1640995200000,
                    'type' => 'CUSTOMER_CREATED',
                    'content' => ['customerId' => 456]
                ],
                [
                    'timestamp' => 1640995260000,
                    'type' => 'CUSTOMER_UPDATED',
                    'content' => ['customerId' => 456]
                ],
                [
                    'timestamp' => 1640995320000,
                    'type' => 'APPOINTMENT_BOOKING_CREATED',
                    'content' => ['bookingId' => 789]
                ]
            ]
        ];

        $request = WebhookEventRequest::from($webhookData);
        $stats = $this->handler->getProcessingStats($request);

        expect($stats)->toMatchArray([
            'total_events' => 3,
            'unique_types' => 3,
            'event_types' => ['CUSTOMER_CREATED', 'CUSTOMER_UPDATED', 'APPOINTMENT_BOOKING_CREATED'],
            'entity_id' => 123,
            'request_uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'has_multiple_events' => true,
            'category_breakdown' => [
                'studio' => 0,
                'customer' => 2,
                'contract' => 0,
                'booking' => 1,
                'class' => 0,
                'device' => 0,
                'employee' => 0,
                'finance' => 0,
                'export' => 0,
                'unknown' => 0
            ]
        ]);
    });

    test('dispatches category-specific events correctly', function () {
        Event::fake();

        $testCases = [
            'CUSTOMER_CREATED' => 'webhook.customer',
            'CONTRACT_CREATED' => 'webhook.contract',
            'APPOINTMENT_BOOKING_CREATED' => 'webhook.booking',
            'EMPLOYEE_CREATED' => 'webhook.employee',
            'DEVICE_CREATED' => 'webhook.device'
        ];

        foreach ($testCases as $eventType => $expectedEvent) {
            Event::fake();

            $webhookData = [
                'entityId' => 123,
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'payload' => [
                    [
                        'timestamp' => 1640995200000,
                        'type' => $eventType,
                        'content' => ['id' => 456]
                    ]
                ]
            ];

            $request = WebhookEventRequest::from($webhookData);
            $this->handler->handle($request);

            Event::assertDispatched($expectedEvent);
        }
    });

    test('handles processing exceptions properly', function () {

        // Create a mock event that will cause an exception during processing
        $webhookData = [
            'entityId' => 123,
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'payload' => [
                [
                    'timestamp' => 1640995200000,
                    'type' => 'CUSTOMER_CREATED',
                    'content' => ['customerId' => 456]
                ]
            ]
        ];

        $request = WebhookEventRequest::from($webhookData);

        // Mock Event to throw an exception
        Event::shouldReceive('dispatch')
            ->andThrow(new \Exception('Test exception'));

        expect(fn() => $this->handler->handle($request))
            ->toThrow(\Exception::class);

    });

    test('generates correct event class names', function () {
        $reflection = new \ReflectionClass($this->handler);
        $method = $reflection->getMethod('generateEventClassName');
        $method->setAccessible(true);

        expect($method->invoke($this->handler, 'CUSTOMER_CREATED'))->toBe('CustomerCreated');
        expect($method->invoke($this->handler, 'CONTRACT_SIGNED'))->toBe('ContractSigned');
        expect($method->invoke($this->handler, 'PAYMENT_PROCESSED'))->toBe('PaymentProcessed');
    });