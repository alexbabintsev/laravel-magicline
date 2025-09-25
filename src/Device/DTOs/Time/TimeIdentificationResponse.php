<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Time;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

class TimeIdentificationResponse extends BaseDto
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