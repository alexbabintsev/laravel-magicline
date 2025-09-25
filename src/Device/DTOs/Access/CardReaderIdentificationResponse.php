<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Access;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

class CardReaderIdentificationResponse extends BaseDto
{
    public string $text;
    public bool $success;

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getText(): string
    {
        return $this->text;
    }
}