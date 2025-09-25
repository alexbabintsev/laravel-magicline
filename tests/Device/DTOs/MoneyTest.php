<?php

use AlexBabintsev\Magicline\Device\DTOs\Money;

it('can create money with amount and currency', function () {
    $money = Money::create(12.50, 'EUR');

    expect($money->amount)->toBe(12.50);
    expect($money->currency)->toBe('EUR');
});

it('can convert amount to cents', function () {
    $money = Money::create(12.50, 'EUR');

    expect($money->toCents())->toBe(1250);
});

it('can create money from cents', function () {
    $money = Money::fromCents(1250, 'EUR');

    expect($money->amount)->toBe(12.50);
    expect($money->currency)->toBe('EUR');
});

it('can format money', function () {
    $money = Money::create(12.50, 'EUR');

    expect($money->format())->toBe('12.50 EUR');
});

it('can format money with custom currency', function () {
    $money = Money::create(12.50, 'EUR');

    expect($money->format('USD'))->toBe('12.50 USD');
});

it('can check if amount is positive', function () {
    $positiveMoney = Money::create(12.50, 'EUR');
    $negativeMoney = Money::create(-12.50, 'EUR');
    $zeroMoney = Money::create(0.00, 'EUR');

    expect($positiveMoney->isPositive())->toBeTrue();
    expect($negativeMoney->isPositive())->toBeFalse();
    expect($zeroMoney->isPositive())->toBeFalse();
});

it('can check if amount is negative', function () {
    $positiveMoney = Money::create(12.50, 'EUR');
    $negativeMoney = Money::create(-12.50, 'EUR');
    $zeroMoney = Money::create(0.00, 'EUR');

    expect($positiveMoney->isNegative())->toBeFalse();
    expect($negativeMoney->isNegative())->toBeTrue();
    expect($zeroMoney->isNegative())->toBeFalse();
});

it('can check if amount is zero', function () {
    $positiveMoney = Money::create(12.50, 'EUR');
    $zeroMoney = Money::create(0.00, 'EUR');

    expect($positiveMoney->isZero())->toBeFalse();
    expect($zeroMoney->isZero())->toBeTrue();
});

it('handles floating point precision correctly', function () {
    $money = Money::create(0.1 + 0.2, 'EUR');

    expect($money->toCents())->toBe(30);
    expect($money->format())->toBe('0.30 EUR');
});