<?php

namespace AlexBabintsev\Magicline\Webhooks\DTOs;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

class WebhookEventRequest extends BaseDto
{
    public int $entityId;

    public string $uuid;

    /** @var WebhookEvent[] */
    public array $payload;

    protected function __construct(array $data)
    {
        $this->entityId = $data['entityId'];
        $this->uuid = $data['uuid'];
        $this->payload = array_map(
            fn ($event) => WebhookEvent::from($event),
            $data['payload'] ?? []
        );
    }

    /**
     * Get entity ID
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * Get unique event UUID
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Get all events in payload
     *
     * @return WebhookEvent[]
     */
    public function getEvents(): array
    {
        return $this->payload;
    }

    /**
     * Get first event (most common case)
     */
    public function getFirstEvent(): ?WebhookEvent
    {
        return $this->payload[0] ?? null;
    }

    /**
     * Get events of specific type
     *
     * @return WebhookEvent[]
     */
    public function getEventsByType(string $type): array
    {
        return array_filter(
            $this->payload,
            fn (WebhookEvent $event) => $event->isType($type)
        );
    }

    /**
     * Check if request contains events of specific type
     */
    public function hasEventType(string $type): bool
    {
        return ! empty($this->getEventsByType($type));
    }

    /**
     * Get count of events in payload
     */
    public function getEventCount(): int
    {
        return count($this->payload);
    }

    /**
     * Check if request has multiple events
     */
    public function hasMultipleEvents(): bool
    {
        return $this->getEventCount() > 1;
    }

    /**
     * Get all unique event types in payload
     */
    public function getEventTypes(): array
    {
        return array_unique(
            array_map(
                fn (WebhookEvent $event) => $event->getType(),
                $this->payload
            )
        );
    }
}
