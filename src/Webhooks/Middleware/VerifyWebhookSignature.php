<?php

namespace AlexBabintsev\Magicline\Webhooks\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        $apiKey = $this->getExpectedApiKey();

        if (!$apiKey) {
            Log::warning('Webhook API key not configured');
            return response()->json([
                'error' => 'Webhook authentication not configured'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $providedKey = $request->header('X-API-KEY');

        if (!$providedKey) {
            Log::warning('Webhook request missing X-API-KEY header', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'error' => 'Missing X-API-KEY header'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!hash_equals($apiKey, $providedKey)) {
            Log::warning('Webhook request with invalid API key', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'provided_key_length' => strlen($providedKey)
            ]);

            return response()->json([
                'error' => 'Invalid X-API-KEY'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Validate content type
        if (!$request->isJson()) {
            Log::warning('Webhook request with invalid content type', [
                'content_type' => $request->header('Content-Type'),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'error' => 'Content-Type must be application/json'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate request method
        if (!$request->isMethod('POST')) {
            Log::warning('Webhook request with invalid method', [
                'method' => $request->method(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'error' => 'Method not allowed. Only POST requests are supported'
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        Log::debug('Webhook authentication successful', [
            'ip' => $request->ip(),
            'content_length' => $request->header('Content-Length')
        ]);

        return $next($request);
    }

    /**
     * Get expected API key from configuration
     */
    protected function getExpectedApiKey(): ?string
    {
        return config('magicline.webhooks.api_key');
    }

    /**
     * Verify webhook payload structure
     */
    protected function validatePayloadStructure(Request $request): bool
    {
        $payload = $request->json()->all();

        $requiredFields = ['entityId', 'uuid', 'payload'];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $payload)) {
                Log::warning('Webhook payload missing required field', [
                    'field' => $field,
                    'ip' => $request->ip()
                ]);
                return false;
            }
        }

        return true;
    }
}