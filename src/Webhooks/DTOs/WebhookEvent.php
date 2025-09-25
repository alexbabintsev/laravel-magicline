<?php

namespace AlexBabintsev\Magicline\Webhooks\DTOs;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;
use AlexBabintsev\Magicline\Webhooks\WebhookEventTypes;
use Carbon\Carbon;

class WebhookEvent extends BaseDto
{
    public int $timestamp;
    public string $type;

    protected function __construct(array $data)
    {
        $this->timestamp = $data['timestamp'];
        $this->type = $data['type'];
    }

    /**
     * Get timestamp as Carbon instance
     */
    public function getTimestamp(): Carbon
    {
        return Carbon::createFromTimestampMs($this->timestamp);
    }

    /**
     * Get event type
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * Check if event is of specific type
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Check if event is customer related
     */
    public function isCustomerEvent(): bool
    {
        return str_starts_with($this->type, 'CUSTOMER_');
    }

    /**
     * Check if event is contract related
     */
    public function isContractEvent(): bool
    {
        return str_starts_with($this->type, 'CONTRACT_');
    }

    /**
     * Check if event is payment related
     */
    public function isPaymentEvent(): bool
    {
        return str_starts_with($this->type, 'PAYMENT_');
    }

    /**
     * Check if event is membership related
     */
    public function isMembershipEvent(): bool
    {
        return str_starts_with($this->type, 'MEMBERSHIP_');
    }

    /**
     * Check if event is appointment related
     */
    public function isAppointmentEvent(): bool
    {
        return str_starts_with($this->type, 'APPOINTMENT_');
    }

    /**
     * Check if event is checkin related
     */
    public function isCheckinEvent(): bool
    {
        return in_array($this->type, ['CUSTOMER_CHECKIN', 'CUSTOMER_CHECKOUT', 'AUTOMATIC_CUSTOMER_CHECKOUT'], true);
    }

    /**
     * Get entity ID for this event type
     * Returns the ID that corresponds to this event type (e.g., customerId for CUSTOMER_CREATED)
     */
    public function getEntityTypeDescription(): string
    {
        return WebhookEventTypes::getEntityIdField($this->type);
    }

    /**
     * Get event category
     */
    public function getCategory(): string
    {
        return WebhookEventTypes::getEventCategory($this->type);
    }

    /**
     * Get processing priority for this event
     */
    public function getProcessingPriority(): int
    {
        return WebhookEventTypes::getProcessingPriority($this->type);
    }

    /**
     * Get human-readable description for this event
     */
    public function getDescription(): string
    {
        return WebhookEventTypes::getDescription($this->type);
    }

    /**
     * Get recommended API endpoint for fetching detailed data
     */
    public function getRecommendedApiEndpoint(int $entityId): string
    {
        return WebhookEventTypes::getRecommendedApiEndpoint($this->type, $entityId);
    }

    /**
     * Check if this event type is supported
     */
    public function isSupported(): bool
    {
        return WebhookEventTypes::isSupported($this->type);
    }
}