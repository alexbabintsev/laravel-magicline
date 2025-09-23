<?php

use alexbabintsev\Magicline\Exceptions\MagiclineApiException;
use alexbabintsev\Magicline\Exceptions\MagiclineAuthenticationException;
use alexbabintsev\Magicline\Exceptions\MagiclineAuthorizationException;
use alexbabintsev\Magicline\Exceptions\MagiclineException;
use alexbabintsev\Magicline\Exceptions\MagiclineValidationException;

test('exception stores http status code', function () {
    $exception = new MagiclineApiException(
        'Test error',
        400,
        'TEST_ERROR',
        ['field' => 'value']
    );

    expect($exception->getHttpStatusCode())->toBe(400);
});

test('exception stores error code', function () {
    $exception = new MagiclineValidationException(
        'Validation failed',
        400,
        'VALIDATION_ERROR',
        ['errors' => ['field required']]
    );

    expect($exception->getErrorCode())->toBe('VALIDATION_ERROR');
});

test('exception stores error details', function () {
    $details = ['field' => 'required', 'value' => 'invalid'];
    $exception = new MagiclineValidationException(
        'Error message',
        400,
        'ERROR_CODE',
        $details
    );

    expect($exception->getErrorDetails())->toBe($details);
});

test('authentication exception extends base', function () {
    $exception = new MagiclineAuthenticationException('Auth failed', 401);

    expect($exception)->toBeInstanceOf(MagiclineException::class)
        ->and($exception->getHttpStatusCode())->toBe(401)
        ->and($exception->getMessage())->toBe('Auth failed');
});

test('authorization exception extends base', function () {
    $exception = new MagiclineAuthorizationException('Access denied', 403);

    expect($exception)->toBeInstanceOf(MagiclineException::class)
        ->and($exception->getHttpStatusCode())->toBe(403);
});

test('validation exception extends base', function () {
    $exception = new MagiclineValidationException('Invalid data', 400);

    expect($exception)->toBeInstanceOf(MagiclineException::class)
        ->and($exception->getHttpStatusCode())->toBe(400);
});

test('api exception extends base', function () {
    $exception = new MagiclineApiException('Server error', 500);

    expect($exception)->toBeInstanceOf(MagiclineException::class)
        ->and($exception->getHttpStatusCode())->toBe(500);
});

test('exception with null error code', function () {
    $exception = new MagiclineApiException('Test error', 500, null, []);

    expect($exception->getErrorCode())->toBeNull();
});

test('exception with empty error details', function () {
    $exception = new MagiclineApiException('Test error', 500, 'ERROR', []);

    expect($exception->getErrorDetails())->toBe([]);
});

test('exception with previous exception', function () {
    $previous = new \Exception('Previous error');
    $exception = new MagiclineApiException('Current error', 500, 'ERROR', [], $previous);

    expect($exception->getPrevious())->toBe($previous);
});
