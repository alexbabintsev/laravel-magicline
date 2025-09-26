<?php

namespace AlexBabintsev\Magicline\Webhooks;

/**
 * Webhook Event Types and Entity ID Mappings
 *
 * Based on official Magicline Webhook API documentation.
 * Each webhook event has an entityId field that corresponds to the event type.
 *
 * Applications should use these events as notifications and make
 * subsequent API calls to get detailed information.
 */
class WebhookEventTypes
{
    // Studio events
    public const ADDITIONAL_INFORMATION_FIELDS_UPDATED = 'ADDITIONAL_INFORMATION_FIELDS_UPDATED';

    // Aggregator events
    public const AGGREGATOR_MEMBER_CREATED = 'AGGREGATOR_MEMBER_CREATED';

    // Appointment booking events
    public const APPOINTMENT_BOOKING_CANCELLED = 'APPOINTMENT_BOOKING_CANCELLED';

    public const APPOINTMENT_BOOKING_CREATED = 'APPOINTMENT_BOOKING_CREATED';

    public const APPOINTMENT_BOOKING_UPDATED = 'APPOINTMENT_BOOKING_UPDATED';

    // Checkout events
    public const AUTOMATIC_CUSTOMER_CHECKOUT = 'AUTOMATIC_CUSTOMER_CHECKOUT';

    // Contract events
    public const CONTRACT_UPDATED = 'CONTRACT_UPDATED';

    public const CONTRACT_CREATED = 'CONTRACT_CREATED';

    public const CONTRACT_CANCELLED = 'CONTRACT_CANCELLED';

    // Customer events
    public const CUSTOMER_CHECKIN = 'CUSTOMER_CHECKIN';

    public const CUSTOMER_CHECKOUT = 'CUSTOMER_CHECKOUT';

    public const CUSTOMER_CREATED = 'CUSTOMER_CREATED';

    public const CUSTOMER_DELETED = 'CUSTOMER_DELETED';

    public const CUSTOMER_UPDATED = 'CUSTOMER_UPDATED';

    public const CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED = 'CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED';

    public const CUSTOMER_ACCESS_DISABLED = 'CUSTOMER_ACCESS_DISABLED';

    // Class booking events
    public const CLASS_BOOKING_CANCELLED = 'CLASS_BOOKING_CANCELLED';

    public const CLASS_BOOKING_CREATED = 'CLASS_BOOKING_CREATED';

    public const CLASS_BOOKING_UPDATED = 'CLASS_BOOKING_UPDATED';

    // Class slot events
    public const CLASS_SLOT_CANCELLED = 'CLASS_SLOT_CANCELLED';

    public const CLASS_SLOT_UPDATED = 'CLASS_SLOT_UPDATED';

    // Device events
    public const DEVICE_CREATED = 'DEVICE_CREATED';

    // Employee events
    public const EMPLOYEE_CREATED = 'EMPLOYEE_CREATED';

    public const EMPLOYEE_DELETED = 'EMPLOYEE_DELETED';

    public const EMPLOYEE_UPDATED = 'EMPLOYEE_UPDATED';

    // Finance events
    public const FINANCE_DEBT_COLLECTION_RUN_CREATED = 'FINANCE_DEBT_COLLECTION_RUN_CREATED';

    public const FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED = 'FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED';

    // Tax advisor events
    public const TAX_ADVISOR_EXPORT_CREATED = 'TAX_ADVISOR_EXPORT_CREATED';

    /**
     * Get all supported event types
     */
    public static function getAllEventTypes(): array
    {
        $reflection = new \ReflectionClass(self::class);

        return array_values($reflection->getConstants());
    }

    /**
     * Get entity ID field name for given event type
     * Based on official Magicline documentation
     *
     * @param  string  $eventType  The webhook event type
     * @return string The corresponding entity ID field name
     */
    public static function getEntityIdField(string $eventType): string
    {
        return match ($eventType) {
            // Studio events
            self::ADDITIONAL_INFORMATION_FIELDS_UPDATED => 'studioId',
            self::AUTOMATIC_CUSTOMER_CHECKOUT => 'studioId',

            // Customer events
            self::AGGREGATOR_MEMBER_CREATED,
            self::CUSTOMER_CHECKIN,
            self::CUSTOMER_CHECKOUT,
            self::CUSTOMER_CREATED,
            self::CUSTOMER_DELETED,
            self::CUSTOMER_UPDATED,
            self::CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED,
            self::CUSTOMER_ACCESS_DISABLED,
            self::CONTRACT_UPDATED,
            self::CONTRACT_CREATED,
            self::CONTRACT_CANCELLED => 'customerId',

            // Booking events
            self::APPOINTMENT_BOOKING_CANCELLED,
            self::APPOINTMENT_BOOKING_CREATED,
            self::APPOINTMENT_BOOKING_UPDATED,
            self::CLASS_BOOKING_CANCELLED,
            self::CLASS_BOOKING_CREATED,
            self::CLASS_BOOKING_UPDATED => 'bookingId',

            // Class slot events
            self::CLASS_SLOT_CANCELLED,
            self::CLASS_SLOT_UPDATED => 'classSlotId',

            // Device events
            self::DEVICE_CREATED => 'deviceId',

            // Employee events
            self::EMPLOYEE_CREATED,
            self::EMPLOYEE_DELETED,
            self::EMPLOYEE_UPDATED => 'employeeId',

            // Finance events
            self::FINANCE_DEBT_COLLECTION_RUN_CREATED => 'debtCollectionRunId',
            self::FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED => 'studioId',

            // Tax advisor events
            self::TAX_ADVISOR_EXPORT_CREATED => 'exportId',

            default => 'entityId'
        };
    }

    /**
     * Get event category for given event type
     */
    public static function getEventCategory(string $eventType): string
    {
        return match ($eventType) {
            // Studio category
            self::ADDITIONAL_INFORMATION_FIELDS_UPDATED,
            self::AUTOMATIC_CUSTOMER_CHECKOUT,
            self::FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED => 'studio',

            // Customer category
            self::CUSTOMER_CHECKIN,
            self::CUSTOMER_CHECKOUT,
            self::CUSTOMER_CREATED,
            self::CUSTOMER_DELETED,
            self::CUSTOMER_UPDATED,
            self::CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED,
            self::CUSTOMER_ACCESS_DISABLED,
            self::AGGREGATOR_MEMBER_CREATED => 'customer',

            // Contract category
            self::CONTRACT_UPDATED,
            self::CONTRACT_CREATED,
            self::CONTRACT_CANCELLED => 'contract',

            // Booking category
            self::APPOINTMENT_BOOKING_CANCELLED,
            self::APPOINTMENT_BOOKING_CREATED,
            self::APPOINTMENT_BOOKING_UPDATED,
            self::CLASS_BOOKING_CANCELLED,
            self::CLASS_BOOKING_CREATED,
            self::CLASS_BOOKING_UPDATED => 'booking',

            // Class category
            self::CLASS_SLOT_CANCELLED,
            self::CLASS_SLOT_UPDATED => 'class',

            // Device category
            self::DEVICE_CREATED => 'device',

            // Employee category
            self::EMPLOYEE_CREATED,
            self::EMPLOYEE_DELETED,
            self::EMPLOYEE_UPDATED => 'employee',

            // Finance category
            self::FINANCE_DEBT_COLLECTION_RUN_CREATED => 'finance',

            // Export category
            self::TAX_ADVISOR_EXPORT_CREATED => 'export',

            default => 'unknown'
        };
    }

    /**
     * Check if event type is supported
     */
    public static function isSupported(string $eventType): bool
    {
        return in_array($eventType, self::getAllEventTypes(), true);
    }

    /**
     * Get recommended API endpoint for fetching detailed data
     * Applications should call these endpoints after receiving webhook notifications
     */
    public static function getRecommendedApiEndpoint(string $eventType, int $entityId): string
    {
        $category = self::getEventCategory($eventType);

        return match ($category) {
            'customer' => "/customers/{$entityId}",
            'contract' => "/customers/{$entityId}", // Contract events use customerId
            'booking' => "/bookings/{$entityId}",
            'class' => "/classes/slots/{$entityId}",
            'device' => "/devices/{$entityId}",
            'employee' => "/employees/{$entityId}",
            'studio' => "/studios/{$entityId}",
            'finance' => "/finance/debt-collection/{$entityId}",
            'export' => "/exports/{$entityId}",
            default => "/entities/{$entityId}"
        };
    }

    /**
     * Get processing priority for event type (1 = highest, 5 = lowest)
     * This can be used for queue prioritization
     */
    public static function getProcessingPriority(string $eventType): int
    {
        return match ($eventType) {
            // Highest priority - real-time events
            self::CUSTOMER_CHECKIN,
            self::CUSTOMER_CHECKOUT,
            self::AUTOMATIC_CUSTOMER_CHECKOUT => 1,

            // High priority - business critical events
            self::CONTRACT_CREATED,
            self::CONTRACT_CANCELLED,
            self::CUSTOMER_ACCESS_DISABLED => 2,

            // Medium-high priority - customer and contract updates
            self::CUSTOMER_CREATED,
            self::CUSTOMER_UPDATED,
            self::CUSTOMER_DELETED,
            self::CONTRACT_UPDATED,
            self::AGGREGATOR_MEMBER_CREATED => 3,

            // Medium priority - bookings and employee events
            self::APPOINTMENT_BOOKING_CREATED,
            self::APPOINTMENT_BOOKING_CANCELLED,
            self::APPOINTMENT_BOOKING_UPDATED,
            self::CLASS_BOOKING_CREATED,
            self::CLASS_BOOKING_CANCELLED,
            self::CLASS_BOOKING_UPDATED,
            self::EMPLOYEE_CREATED,
            self::EMPLOYEE_UPDATED,
            self::EMPLOYEE_DELETED => 4,

            // Lower priority - system and administrative events
            self::CLASS_SLOT_CANCELLED,
            self::CLASS_SLOT_UPDATED,
            self::DEVICE_CREATED,
            self::ADDITIONAL_INFORMATION_FIELDS_UPDATED,
            self::CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED,
            self::FINANCE_DEBT_COLLECTION_RUN_CREATED,
            self::FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED,
            self::TAX_ADVISOR_EXPORT_CREATED => 5,

            default => 5 // Lowest priority for unknown events
        };
    }

    /**
     * Get additional content fields for specific event types
     * Some events have additional data in the content field
     */
    public static function getAdditionalContentFields(string $eventType): array
    {
        return match ($eventType) {
            self::AGGREGATOR_MEMBER_CREATED => ['aggregatorId'],
            self::CONTRACT_UPDATED,
            self::CONTRACT_CREATED,
            self::CONTRACT_CANCELLED => ['contractId'],
            self::CLASS_SLOT_CANCELLED,
            self::CLASS_SLOT_UPDATED => ['classId'],
            self::AUTOMATIC_CUSTOMER_CHECKOUT => ['checkouts'],
            default => []
        };
    }

    /**
     * Get human-readable description for event type
     */
    public static function getDescription(string $eventType): string
    {
        return match ($eventType) {
            self::ADDITIONAL_INFORMATION_FIELDS_UPDATED => 'Additional information field was updated',
            self::AGGREGATOR_MEMBER_CREATED => 'Aggregator member was created',
            self::APPOINTMENT_BOOKING_CANCELLED => 'Appointment booking was cancelled',
            self::APPOINTMENT_BOOKING_CREATED => 'Appointment booking was created',
            self::APPOINTMENT_BOOKING_UPDATED => 'Appointment booking time or resource was updated',
            self::AUTOMATIC_CUSTOMER_CHECKOUT => 'One or multiple customers were automatically checked out',
            self::CONTRACT_UPDATED => 'Contract was changed',
            self::CONTRACT_CREATED => 'Main contract was created',
            self::CONTRACT_CANCELLED => 'Contract was cancelled',
            self::CUSTOMER_CHECKIN => 'Customer has physically checked in at facility',
            self::CUSTOMER_CHECKOUT => 'Customer has physically checked out from facility',
            self::CUSTOMER_CREATED => 'Customer has been created at facility',
            self::CUSTOMER_DELETED => 'Customer has been deleted',
            self::CUSTOMER_UPDATED => 'Customer\'s data has been changed',
            self::CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED => 'Customer\'s communication preferences are updated',
            self::CUSTOMER_ACCESS_DISABLED => 'Customer\'s access was disabled',
            self::CLASS_BOOKING_CANCELLED => 'Class booking was cancelled',
            self::CLASS_BOOKING_CREATED => 'Class booking was created',
            self::CLASS_BOOKING_UPDATED => 'Class booking was updated',
            self::CLASS_SLOT_CANCELLED => 'Class slot was cancelled',
            self::CLASS_SLOT_UPDATED => 'Class slot time or resource was updated',
            self::DEVICE_CREATED => 'Device was created',
            self::EMPLOYEE_CREATED => 'Employee has been created at facility',
            self::EMPLOYEE_DELETED => 'Employee has been deleted',
            self::EMPLOYEE_UPDATED => 'Employee\'s data has been changed',
            self::FINANCE_DEBT_COLLECTION_RUN_CREATED => 'Debt collection run was created',
            self::FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED => 'Debt collection configuration was updated',
            self::TAX_ADVISOR_EXPORT_CREATED => 'Tax advisor export was created',
            default => 'Unknown event type'
        };
    }
}
