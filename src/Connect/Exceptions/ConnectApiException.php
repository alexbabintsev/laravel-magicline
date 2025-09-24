<?php

namespace AlexBabintsev\Magicline\Connect\Exceptions;

use Exception;

class ConnectApiException extends Exception
{
    public function __construct(
        string $message = 'Connect API request failed',
        private readonly int $httpStatusCode = 0,
        private readonly ?string $errorCode = null,
        private readonly array $errorDetails = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, $httpStatusCode, $previous);
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorDetails(): array
    {
        return $this->errorDetails;
    }

    public function hasErrorDetails(): bool
    {
        return !empty($this->errorDetails);
    }
}