<?php

namespace AlexBabintsev\Magicline\Webhooks\Http\Controllers;

use AlexBabintsev\Magicline\Webhooks\DTOs\WebhookEventRequest;
use AlexBabintsev\Magicline\Webhooks\Exceptions\WebhookProcessingException;
use AlexBabintsev\Magicline\Webhooks\WebhookHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    public function __construct(
        private readonly WebhookHandler $webhookHandler
    ) {}

    /**
     * Handle incoming webhook requests
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Validate request payload structure
            $validator = $this->validatePayload($request);

            if ($validator->fails()) {
                Log::warning('Webhook request validation failed', [
                    'errors' => $validator->errors(),
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'error' => 'Invalid payload structure',
                    'details' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $payload = $request->json()->all();

            // Validate with WebhookHandler
            if (! $this->webhookHandler->validatePayload($payload)) {
                return response()->json([
                    'error' => 'Invalid webhook payload',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create webhook request DTO
            $webhookRequest = WebhookEventRequest::from($payload);

            // Check if we have any events to process
            if ($webhookRequest->getEventCount() === 0) {
                Log::info('Webhook request with empty payload', [
                    'entityId' => $webhookRequest->getEntityId(),
                    'uuid' => $webhookRequest->getUuid(),
                ]);

                return response()->json([
                    'message' => 'No events to process',
                    'processed' => 0,
                ]);
            }

            // Process the webhook request
            $this->webhookHandler->handle($webhookRequest);

            // Get processing statistics
            $stats = $this->webhookHandler->getProcessingStats($webhookRequest);

            Log::info('Webhook request processed successfully', $stats);

            return response()->json([
                'message' => 'Webhook processed successfully',
                'processed' => $stats['total_events'],
                'entity_id' => $stats['entity_id'],
                'request_uuid' => $stats['request_uuid'],
                'event_types' => $stats['event_types'],
            ]);

        } catch (WebhookProcessingException $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => 'An error occurred while processing the webhook',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            Log::error('Unexpected webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Internal server error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get webhook status/health check
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'status' => 'active',
            'service' => 'Magicline Webhooks',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Validate webhook payload structure
     */
    protected function validatePayload(Request $request): \Illuminate\Validation\Validator
    {
        $data = $request->json()->all();
        $rules = [
            'entityId' => 'required|integer|min:1',
            'uuid' => 'required|string|uuid',
            'payload' => 'required|array',
        ];

        // Only validate payload events if they exist
        if (! empty($data['payload'])) {
            $rules['payload.*.timestamp'] = 'required|integer|min:0';
            $rules['payload.*.type'] = 'required|string|min:1';
        }

        return Validator::make($data, $rules, [
            'entityId.required' => 'Entity ID is required',
            'entityId.integer' => 'Entity ID must be an integer',
            'entityId.min' => 'Entity ID must be greater than 0',
            'uuid.required' => 'UUID is required',
            'uuid.uuid' => 'UUID must be a valid UUID format',
            'payload.required' => 'Payload is required',
            'payload.array' => 'Payload must be an array',
            'payload.*.timestamp.required' => 'Event timestamp is required',
            'payload.*.timestamp.integer' => 'Event timestamp must be an integer',
            'payload.*.type.required' => 'Event type is required',
            'payload.*.type.string' => 'Event type must be a string',
        ]);
    }
}
