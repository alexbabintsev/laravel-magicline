<?php

namespace AlexBabintsev\Magicline\Webhooks;

use AlexBabintsev\Magicline\Webhooks\DTOs\WebhookEvent;
use AlexBabintsev\Magicline\Webhooks\DTOs\WebhookEventRequest;
use AlexBabintsev\Magicline\Webhooks\Exceptions\WebhookProcessingException;
use AlexBabintsev\Magicline\Webhooks\WebhookEventTypes;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookHandler
{
    /**
     * Process webhook event request
     */
    public function handle(WebhookEventRequest $request): void
    {
        Log::info('Received webhook request', [
            'entityId' => $request->getEntityId(),
            'uuid' => $request->getUuid(),
            'eventCount' => $request->getEventCount(),
            'eventTypes' => $request->getEventTypes()
        ]);

        // Process events asynchronously as recommended by Magicline documentation
        foreach ($request->getEvents() as $event) {
            $this->scheduleEventProcessing($event, $request);
        }
    }

    /**
     * Schedule asynchronous event processing
     * As per Magicline documentation, webhook processing must be asynchronous
     */
    protected function scheduleEventProcessing(WebhookEvent $event, WebhookEventRequest $request): void
    {
        Log::debug('Scheduling webhook event processing', [
            'type' => $event->getType(),
            'timestamp' => $event->getTimestamp()->toISOString(),
            'entityId' => $request->getEntityId(),
            'entityType' => $event->getEntityTypeDescription()
        ]);

        // Dispatch Laravel events immediately (non-blocking)
        $this->dispatchEvent($event, $request);

        // Note: Applications should use Laravel queues or jobs for actual async processing
        // This ensures webhook endpoint responds quickly (< 5 seconds as required)
        Log::info('Webhook event scheduled for processing', [
            'type' => $event->getType(),
            'entityId' => $request->getEntityId()
        ]);
    }

    /**
     * Process individual webhook event (for synchronous processing if needed)
     */
    public function processEvent(WebhookEvent $event, WebhookEventRequest $request): void
    {
        try {
            Log::debug('Processing webhook event synchronously', [
                'type' => $event->getType(),
                'timestamp' => $event->getTimestamp()->toISOString(),
                'entityId' => $request->getEntityId()
            ]);

            // WARNING: According to Magicline docs, do NOT make API calls during webhook processing
            // This should be done asynchronously via queued jobs

            // Dispatch Laravel event based on webhook type
            $this->dispatchEvent($event, $request);

            Log::info('Webhook event processed successfully', [
                'type' => $event->getType(),
                'entityId' => $request->getEntityId()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process webhook event', [
                'type' => $event->getType(),
                'entityId' => $request->getEntityId(),
                'error' => $e->getMessage()
            ]);

            throw new WebhookProcessingException(
                "Failed to process webhook event of type '{$event->getType()}': {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    /**
     * Dispatch Laravel event for webhook processing
     */
    protected function dispatchEvent(WebhookEvent $event, WebhookEventRequest $request): void
    {
        // Create event class name from webhook type
        $eventClassName = $this->generateEventClassName($event->getType());

        // Dispatch generic webhook event
        Event::dispatch('webhook.received', [$event, $request]);

        // Dispatch specific event type
        Event::dispatch("webhook.{$event->getType()}", [$event, $request]);

        // Dispatch category-specific events based on comprehensive event types
        $category = WebhookEventTypes::getEventCategory($event->getType());
        Event::dispatch("webhook.{$category}", [$event, $request]);

        // Legacy category events for backward compatibility
        if ($event->isCustomerEvent()) {
            Event::dispatch('webhook.customer', [$event, $request]);
        }

        if ($event->isContractEvent()) {
            Event::dispatch('webhook.contract', [$event, $request]);
        }

        if ($event->isCheckinEvent()) {
            Event::dispatch('webhook.checkin', [$event, $request]);
        }
    }

    /**
     * Generate event class name from webhook type
     */
    protected function generateEventClassName(string $webhookType): string
    {
        // Convert CUSTOMER_CREATED to CustomerCreated
        return Str::studly(strtolower($webhookType));
    }

    /**
     * Validate webhook payload structure
     */
    public function validatePayload(array $payload): bool
    {
        $requiredFields = ['entityId', 'uuid', 'payload'];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $payload)) {
                Log::warning('Webhook payload missing required field', [
                    'field' => $field,
                    'payload' => $payload
                ]);
                return false;
            }
        }

        if (!is_array($payload['payload'])) {
            Log::warning('Webhook payload field must be array', [
                'payload' => $payload
            ]);
            return false;
        }

        return true;
    }

    /**
     * Check if event type is supported
     */
    public function isEventTypeSupported(string $eventType): bool
    {
        return WebhookEventTypes::isSupported($eventType);
    }

    /**
     * Get recommended queue for processing this event type
     * Applications should use Laravel queues for asynchronous processing
     */
    public function getRecommendedQueue(WebhookEvent $event): string
    {
        $category = WebhookEventTypes::getEventCategory($event->getType());
        $priority = WebhookEventTypes::getProcessingPriority($event->getType());

        return "webhook-{$category}-p{$priority}";
    }

    /**
     * Get event processing statistics
     */
    public function getProcessingStats(WebhookEventRequest $request): array
    {
        $events = $request->getEvents();
        $types = $request->getEventTypes();

        $stats = [
            'total_events' => count($events),
            'unique_types' => count($types),
            'event_types' => $types,
            'entity_id' => $request->getEntityId(),
            'request_uuid' => $request->getUuid(),
            'has_multiple_events' => $request->hasMultipleEvents(),
            'category_breakdown' => [
                'studio' => 0,
                'customer' => 0,
                'contract' => 0,
                'booking' => 0,
                'class' => 0,
                'device' => 0,
                'employee' => 0,
                'finance' => 0,
                'export' => 0,
                'unknown' => 0
            ]
        ];

        foreach ($events as $event) {
            $category = WebhookEventTypes::getEventCategory($event->getType());

            if (array_key_exists($category, $stats['category_breakdown'])) {
                $stats['category_breakdown'][$category]++;
            } else {
                $stats['category_breakdown']['unknown']++;
            }
        }

        return $stats;
    }
}