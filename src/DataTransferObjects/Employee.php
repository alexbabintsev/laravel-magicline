<?php

namespace AlexBabintsev\Magicline\DataTransferObjects;

class Employee extends BaseDto
{
    public ?int $id = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $position = null;

    public ?string $department = null;

    public ?string $hireDate = null;

    public ?bool $isActive = null;
}
