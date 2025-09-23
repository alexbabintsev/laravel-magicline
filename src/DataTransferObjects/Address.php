<?php

namespace AlexBabintsev\Magicline\DataTransferObjects;

class Address extends BaseDto
{
    public ?string $street = null;

    public ?string $city = null;

    public ?string $postalCode = null;

    public ?string $country = null;

    public ?string $state = null;
}
