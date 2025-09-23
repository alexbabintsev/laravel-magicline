<?php

namespace AlexBabintsev\Magicline\DataTransferObjects;

class Appointment extends BaseDto
{
    public ?int $id = null;

    public ?string $title = null;

    public ?string $description = null;

    public ?string $startTime = null;

    public ?string $endTime = null;

    public ?string $status = null;

    public ?int $customerId = null;

    public ?int $employeeId = null;

    public ?string $type = null;

    public ?bool $isBookable = null;
}
