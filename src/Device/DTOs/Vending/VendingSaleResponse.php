<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Vending;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

class VendingSaleResponse extends BaseDto
{
    public string $text;

    public bool $success;

    public string $transactionId;

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}
