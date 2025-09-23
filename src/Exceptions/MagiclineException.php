<?php

namespace AlexBabintsev\Magicline\Exceptions;

use Exception;

abstract class MagiclineException extends Exception
{
    public function __construct(
        string $message = '',
        protected int $httpStatusCode = 0,
        protected ?string $errorCode = null,
        protected array $errorDetails = [],
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
}
