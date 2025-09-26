<?php

use AlexBabintsev\Magicline\Device\MagiclineDevice;
use AlexBabintsev\Magicline\Device\Resources\AccessResource;
use AlexBabintsev\Magicline\Device\Resources\TimeResource;
use AlexBabintsev\Magicline\Device\Resources\VendingResource;
use Illuminate\Http\Client\Factory;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->httpFactory = mock(Factory::class);
    $this->logger = mock(LoggerInterface::class);

    $this->magiclineDevice = new MagiclineDevice(
        $this->httpFactory,
        'https://api.example.com',
        'test-bearer-token',
        $this->logger
    );
});

it('can get access resource', function () {
    $accessResource = $this->magiclineDevice->access();

    expect($accessResource)->toBeInstanceOf(AccessResource::class);

    // Should return the same instance on subsequent calls
    $secondAccessResource = $this->magiclineDevice->access();
    expect($secondAccessResource)->toBe($accessResource);
});

it('can get vending resource', function () {
    $vendingResource = $this->magiclineDevice->vending();

    expect($vendingResource)->toBeInstanceOf(VendingResource::class);

    // Should return the same instance on subsequent calls
    $secondVendingResource = $this->magiclineDevice->vending();
    expect($secondVendingResource)->toBe($vendingResource);
});

it('can get time resource', function () {
    $timeResource = $this->magiclineDevice->time();

    expect($timeResource)->toBeInstanceOf(TimeResource::class);

    // Should return the same instance on subsequent calls
    $secondTimeResource = $this->magiclineDevice->time();
    expect($secondTimeResource)->toBe($timeResource);
});
