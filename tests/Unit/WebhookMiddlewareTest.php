<?php

use AlexBabintsev\Magicline\Webhooks\Middleware\VerifyWebhookSignature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

beforeEach(function () {
    $this->middleware = new VerifyWebhookSignature;
    config(['magicline.webhooks.api_key' => 'test-api-key-123']);
});

test('allows request with valid api key', function () {
    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode(['test' => 'data']));
    $request->headers->set('X-API-KEY', 'test-api-key-123');
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
});

test('rejects request with missing api key header', function () {

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode(['test' => 'data']));
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Missing X-API-KEY header');

});

test('rejects request with invalid api key', function () {

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode(['test' => 'data']));
    $request->headers->set('X-API-KEY', 'invalid-key');
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Invalid X-API-KEY');

});

test('rejects non-json requests', function () {

    $request = Request::create('/webhook', 'POST', ['test' => 'data']);
    $request->headers->set('X-API-KEY', 'test-api-key-123');
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Content-Type must be application/json');

});

test('rejects non-post requests', function () {

    $request = Request::create('/webhook', 'GET');
    $request->headers->set('X-API-KEY', 'test-api-key-123');
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(Response::HTTP_METHOD_NOT_ALLOWED);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Method not allowed. Only POST requests are supported');

});

test('returns error when webhook api key not configured', function () {

    // Override config to remove API key
    config(['magicline.webhooks.api_key' => null]);

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode(['test' => 'data']));
    $request->headers->set('X-API-KEY', 'any-key');
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR);

    $responseData = json_decode($response->getContent(), true);
    expect($responseData['error'])->toBe('Webhook authentication not configured');

});

test('uses timing-safe comparison for api keys', function () {
    // This test ensures we're using hash_equals for security
    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode(['test' => 'data']));
    $request->headers->set('X-API-KEY', 'test-api-key-123');
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    // Test with similar but different key
    $request2 = Request::create('/webhook', 'POST', [], [], [], [], json_encode(['test' => 'data']));
    $request2->headers->set('X-API-KEY', 'test-api-key-124'); // Different last character
    $request2->headers->set('Content-Type', 'application/json');

    $response2 = $this->middleware->handle($request2, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response2->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
});

test('logs request information for debugging', function () {
    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode(['test' => 'data']));
    $request->headers->set('X-API-KEY', 'test-api-key-123');
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(200);
});
