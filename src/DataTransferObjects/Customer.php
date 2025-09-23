<?php

namespace alexbabintsev\Magicline\DataTransferObjects;

class Customer extends BaseDto
{
    public ?int $id = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $birthDate = null;

    public ?string $gender = null;

    public ?string $status = null;

    public ?string $membershipNumber = null;

    public ?string $registrationDate = null;

    public ?Address $address = null;

    public function __construct(array $data = [])
    {
        if (isset($data['address']) && is_array($data['address'])) {
            $data['address'] = Address::from($data['address']);
        }

        parent::__construct($data);
    }
}
