<?php

namespace alexbabintsev\Magicline\DataTransferObjects;

class ClassDto extends BaseDto
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $description = null;

    public ?string $startTime = null;

    public ?string $endTime = null;

    public ?int $maxParticipants = null;

    public ?int $currentParticipants = null;

    public ?int $instructorId = null;

    public ?string $instructorName = null;

    public ?string $location = null;

    public ?bool $isBookable = null;

    public ?array $equipment = null;
}
