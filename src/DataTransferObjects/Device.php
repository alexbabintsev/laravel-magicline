<?php

namespace alexbabintsev\Magicline\DataTransferObjects;

class Device extends BaseDto
{
    public ?string $id = null;

    public ?string $name = null;

    public ?string $type = null;

    public ?string $status = null;

    public ?string $location = null;

    public ?bool $isActive = null;

    public ?string $lastActivity = null;
}
