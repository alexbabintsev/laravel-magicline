<?php

namespace alexbabintsev\Magicline\Tests\Unit\Exceptions;

use alexbabintsev\Magicline\Exceptions\MagiclineApiException;
use alexbabintsev\Magicline\Exceptions\MagiclineAuthenticationException;
use alexbabintsev\Magicline\Exceptions\MagiclineAuthorizationException;
use alexbabintsev\Magicline\Exceptions\MagiclineException;
use alexbabintsev\Magicline\Exceptions\MagiclineValidationException;
use alexbabintsev\Magicline\Tests\TestCase;

class MagiclineExceptionTest extends TestCase
{
    public function test_exception_stores_http_status_code()
    {
        $exception = new MagiclineApiException(
            'Test error',
            400,
            'TEST_ERROR',
            ['field' => 'value']
        );

        expect($exception->getHttpStatusCode())->toBe(400);
    }

    public function test_exception_stores_error_code()
    {
        $exception = new MagiclineValidationException(
            'Validation failed',
            400,
            'VALIDATION_ERROR',
            ['errors' => ['field required']]
        );

        expect($exception->getErrorCode())->toBe('VALIDATION_ERROR');
    }

    public function test_exception_stores_error_details()
    {
        $details = ['field' => 'required', 'value' => 'invalid'];
        $exception = new MagiclineValidationException(
            'Error message',
            400,
            'ERROR_CODE',
            $details
        );

        expect($exception->getErrorDetails())->toBe($details);
    }

    public function test_authentication_exception_extends_base()
    {
        $exception = new MagiclineAuthenticationException('Auth failed', 401);

        expect($exception)->toBeInstanceOf(MagiclineException::class);
        expect($exception->getHttpStatusCode())->toBe(401);
        expect($exception->getMessage())->toBe('Auth failed');
    }

    public function test_authorization_exception_extends_base()
    {
        $exception = new MagiclineAuthorizationException('Access denied', 403);

        expect($exception)->toBeInstanceOf(MagiclineException::class);
        expect($exception->getHttpStatusCode())->toBe(403);
    }

    public function test_validation_exception_extends_base()
    {
        $exception = new MagiclineValidationException('Invalid data', 400);

        expect($exception)->toBeInstanceOf(MagiclineException::class);
        expect($exception->getHttpStatusCode())->toBe(400);
    }

    public function test_api_exception_extends_base()
    {
        $exception = new MagiclineApiException('Server error', 500);

        expect($exception)->toBeInstanceOf(MagiclineException::class);
        expect($exception->getHttpStatusCode())->toBe(500);
    }

    public function test_exception_with_null_error_code()
    {
        $exception = new MagiclineApiException('Test error', 500, null, []);

        expect($exception->getErrorCode())->toBeNull();
    }

    public function test_exception_with_empty_error_details()
    {
        $exception = new MagiclineApiException('Test error', 500, 'ERROR', []);

        expect($exception->getErrorDetails())->toBe([]);
    }

    public function test_exception_with_previous_exception()
    {
        $previous = new \Exception('Previous error');
        $exception = new MagiclineApiException('Current error', 500, 'ERROR', [], $previous);

        expect($exception->getPrevious())->toBe($previous);
    }
}
