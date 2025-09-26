<?php

use AlexBabintsev\Magicline\Webhooks\WebhookEventTypes;

test('can get all event types', function () {
    $eventTypes = WebhookEventTypes::getAllEventTypes();

    expect($eventTypes)->toBeArray()
        ->and($eventTypes)->toContain('CUSTOMER_CREATED')
        ->and($eventTypes)->toContain('CONTRACT_CREATED')
        ->and($eventTypes)->toContain('APPOINTMENT_BOOKING_CREATED')
        ->and($eventTypes)->toContain('EMPLOYEE_CREATED')
        ->and(count($eventTypes))->toBeGreaterThan(25); // We have 26+ event types
});

test('can get entity id field for event types', function () {
    expect(WebhookEventTypes::getEntityIdField('CUSTOMER_CREATED'))->toBe('customerId');
    expect(WebhookEventTypes::getEntityIdField('CONTRACT_CREATED'))->toBe('customerId');
    expect(WebhookEventTypes::getEntityIdField('APPOINTMENT_BOOKING_CREATED'))->toBe('bookingId');
    expect(WebhookEventTypes::getEntityIdField('EMPLOYEE_CREATED'))->toBe('employeeId');
    expect(WebhookEventTypes::getEntityIdField('DEVICE_CREATED'))->toBe('deviceId');
    expect(WebhookEventTypes::getEntityIdField('CLASS_SLOT_CANCELLED'))->toBe('classSlotId');
    expect(WebhookEventTypes::getEntityIdField('ADDITIONAL_INFORMATION_FIELDS_UPDATED'))->toBe('studioId');
    expect(WebhookEventTypes::getEntityIdField('UNKNOWN_EVENT'))->toBe('entityId');
});

test('can get event categories', function () {
    expect(WebhookEventTypes::getEventCategory('CUSTOMER_CREATED'))->toBe('customer');
    expect(WebhookEventTypes::getEventCategory('CONTRACT_CREATED'))->toBe('contract');
    expect(WebhookEventTypes::getEventCategory('APPOINTMENT_BOOKING_CREATED'))->toBe('booking');
    expect(WebhookEventTypes::getEventCategory('CLASS_SLOT_CANCELLED'))->toBe('class');
    expect(WebhookEventTypes::getEventCategory('EMPLOYEE_CREATED'))->toBe('employee');
    expect(WebhookEventTypes::getEventCategory('DEVICE_CREATED'))->toBe('device');
    expect(WebhookEventTypes::getEventCategory('ADDITIONAL_INFORMATION_FIELDS_UPDATED'))->toBe('studio');
    expect(WebhookEventTypes::getEventCategory('FINANCE_DEBT_COLLECTION_RUN_CREATED'))->toBe('finance');
    expect(WebhookEventTypes::getEventCategory('TAX_ADVISOR_EXPORT_CREATED'))->toBe('export');
    expect(WebhookEventTypes::getEventCategory('UNKNOWN_EVENT'))->toBe('unknown');
});

test('can check if event type is supported', function () {
    expect(WebhookEventTypes::isSupported('CUSTOMER_CREATED'))->toBeTrue();
    expect(WebhookEventTypes::isSupported('CONTRACT_CREATED'))->toBeTrue();
    expect(WebhookEventTypes::isSupported('APPOINTMENT_BOOKING_CREATED'))->toBeTrue();
    expect(WebhookEventTypes::isSupported('UNKNOWN_EVENT'))->toBeFalse();
});

test('can get recommended api endpoints', function () {
    expect(WebhookEventTypes::getRecommendedApiEndpoint('CUSTOMER_CREATED', 123))->toBe('/customers/123');
    expect(WebhookEventTypes::getRecommendedApiEndpoint('CONTRACT_CREATED', 123))->toBe('/customers/123');
    expect(WebhookEventTypes::getRecommendedApiEndpoint('APPOINTMENT_BOOKING_CREATED', 456))->toBe('/bookings/456');
    expect(WebhookEventTypes::getRecommendedApiEndpoint('CLASS_SLOT_CANCELLED', 789))->toBe('/classes/slots/789');
    expect(WebhookEventTypes::getRecommendedApiEndpoint('EMPLOYEE_CREATED', 101))->toBe('/employees/101');
    expect(WebhookEventTypes::getRecommendedApiEndpoint('DEVICE_CREATED', 102))->toBe('/devices/102');
});

test('can get processing priorities', function () {
    // Highest priority - real-time events
    expect(WebhookEventTypes::getProcessingPriority('CUSTOMER_CHECKIN'))->toBe(1);
    expect(WebhookEventTypes::getProcessingPriority('CUSTOMER_CHECKOUT'))->toBe(1);
    expect(WebhookEventTypes::getProcessingPriority('AUTOMATIC_CUSTOMER_CHECKOUT'))->toBe(1);

    // High priority - business critical events
    expect(WebhookEventTypes::getProcessingPriority('CONTRACT_CREATED'))->toBe(2);
    expect(WebhookEventTypes::getProcessingPriority('CONTRACT_CANCELLED'))->toBe(2);
    expect(WebhookEventTypes::getProcessingPriority('CUSTOMER_ACCESS_DISABLED'))->toBe(2);

    // Medium-high priority
    expect(WebhookEventTypes::getProcessingPriority('CUSTOMER_CREATED'))->toBe(3);
    expect(WebhookEventTypes::getProcessingPriority('CONTRACT_UPDATED'))->toBe(3);

    // Medium priority
    expect(WebhookEventTypes::getProcessingPriority('APPOINTMENT_BOOKING_CREATED'))->toBe(4);
    expect(WebhookEventTypes::getProcessingPriority('EMPLOYEE_CREATED'))->toBe(4);

    // Lower priority
    expect(WebhookEventTypes::getProcessingPriority('DEVICE_CREATED'))->toBe(5);
    expect(WebhookEventTypes::getProcessingPriority('TAX_ADVISOR_EXPORT_CREATED'))->toBe(5);
});

test('can get additional content fields', function () {
    expect(WebhookEventTypes::getAdditionalContentFields('AGGREGATOR_MEMBER_CREATED'))->toBe(['aggregatorId']);
    expect(WebhookEventTypes::getAdditionalContentFields('CONTRACT_CREATED'))->toBe(['contractId']);
    expect(WebhookEventTypes::getAdditionalContentFields('CLASS_SLOT_CANCELLED'))->toBe(['classId']);
    expect(WebhookEventTypes::getAdditionalContentFields('AUTOMATIC_CUSTOMER_CHECKOUT'))->toBe(['checkouts']);
    expect(WebhookEventTypes::getAdditionalContentFields('CUSTOMER_CREATED'))->toBe([]);
});

test('can get human-readable descriptions', function () {
    expect(WebhookEventTypes::getDescription('CUSTOMER_CREATED'))->toBe('Customer has been created at facility');
    expect(WebhookEventTypes::getDescription('CONTRACT_CREATED'))->toBe('Main contract was created');
    expect(WebhookEventTypes::getDescription('CUSTOMER_CHECKIN'))->toBe('Customer has physically checked in at facility');
    expect(WebhookEventTypes::getDescription('UNKNOWN_EVENT'))->toBe('Unknown event type');
});

test('handles all official magicline event types', function () {
    $officialEvents = [
        'ADDITIONAL_INFORMATION_FIELDS_UPDATED',
        'AGGREGATOR_MEMBER_CREATED',
        'APPOINTMENT_BOOKING_CANCELLED',
        'APPOINTMENT_BOOKING_CREATED',
        'APPOINTMENT_BOOKING_UPDATED',
        'AUTOMATIC_CUSTOMER_CHECKOUT',
        'CONTRACT_UPDATED',
        'CONTRACT_CREATED',
        'CONTRACT_CANCELLED',
        'CUSTOMER_CHECKIN',
        'CUSTOMER_CHECKOUT',
        'CUSTOMER_CREATED',
        'CUSTOMER_DELETED',
        'CUSTOMER_UPDATED',
        'CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED',
        'CUSTOMER_ACCESS_DISABLED',
        'CLASS_BOOKING_CANCELLED',
        'CLASS_BOOKING_CREATED',
        'CLASS_BOOKING_UPDATED',
        'CLASS_SLOT_CANCELLED',
        'CLASS_SLOT_UPDATED',
        'DEVICE_CREATED',
        'EMPLOYEE_CREATED',
        'EMPLOYEE_DELETED',
        'EMPLOYEE_UPDATED',
        'FINANCE_DEBT_COLLECTION_RUN_CREATED',
        'FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED',
        'TAX_ADVISOR_EXPORT_CREATED',
    ];

    foreach ($officialEvents as $event) {
        expect(WebhookEventTypes::isSupported($event))->toBeTrue("Event {$event} should be supported");
        expect(WebhookEventTypes::getEventCategory($event))->not->toBe('unknown', "Event {$event} should have a known category");
        expect(WebhookEventTypes::getEntityIdField($event))->not->toBe('entityId', "Event {$event} should have a specific entity ID field");
        expect(WebhookEventTypes::getProcessingPriority($event))->toBeGreaterThan(0)->toBeLessThanOrEqual(5);
    }
});
