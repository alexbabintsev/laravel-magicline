<?php

use AlexBabintsev\Magicline\DataTransferObjects\Address;
use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

class TestDto extends BaseDto
{
    public string $name = '';

    public int $age = 0;

    public ?string $email = null;

    public ?Address $address = null;

    public array $tags = [];
}

class TestDtoWithNestedArray extends BaseDto
{
    public array $addresses = [];
}

test('base dto handles nested dto in toArray', function () {
    $address = new Address(['street' => 'Main St', 'city' => 'Berlin']);
    $dto = new TestDto(['name' => 'John', 'address' => $address]);

    $result = $dto->toArray();

    expect($result['address'])->toBe(['street' => 'Main St', 'city' => 'Berlin', 'postalCode' => null, 'country' => null, 'state' => null]);
});

test('base dto handles array of dtos in toArray', function () {
    $addresses = [
        new Address(['street' => 'First St', 'city' => 'Berlin']),
        new Address(['street' => 'Second St', 'city' => 'Hamburg']),
    ];
    $dto = new TestDtoWithNestedArray(['addresses' => $addresses]);

    $result = $dto->toArray();

    expect($result['addresses'])->toHaveCount(2)
        ->and($result['addresses'][0])->toBe(['street' => 'First St', 'city' => 'Berlin', 'postalCode' => null, 'country' => null, 'state' => null])
        ->and($result['addresses'][1])->toBe(['street' => 'Second St', 'city' => 'Hamburg', 'postalCode' => null, 'country' => null, 'state' => null]);
});
