<?php

namespace AlexBabintsev\Magicline\DataTransferObjects;

class MembershipOffer extends BaseDto
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $description = null;

    public ?float $price = null;

    public ?string $currency = null;

    public ?int $durationInMonths = null;

    public ?string $type = null;

    public ?bool $isActive = null;

    public ?array $benefits = null;
}
