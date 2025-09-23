<?php

use AlexBabintsev\Magicline\Facades\Magicline;

test('facade accessor returns correct class', function () {
    $facade = new \AlexBabintsev\Magicline\Facades\Magicline();
    $reflection = new ReflectionMethod($facade, 'getFacadeAccessor');
    $reflection->setAccessible(true);
    $accessor = $reflection->invoke($facade);

    expect($accessor)->toBe(\AlexBabintsev\Magicline\Magicline::class);
});

test('facade returns magicline instance', function () {
    $instance = Magicline::getFacadeRoot();

    expect($instance)->toBeInstanceOf(\AlexBabintsev\Magicline\Magicline::class);
});

test('facade can access customers resource', function () {
    $customers = Magicline::customers();

    expect($customers)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Customers::class);
});

test('facade can access appointments resource', function () {
    $appointments = Magicline::appointments();

    expect($appointments)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Appointments::class);
});

test('facade can access classes resource', function () {
    $classes = Magicline::classes();

    expect($classes)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Classes::class);
});

test('facade can access checkin vouchers resource', function () {
    $checkinVouchers = Magicline::checkinVouchers();

    expect($checkinVouchers)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CheckinVouchers::class);
});

test('facade can access cross studio resource', function () {
    $crossStudio = Magicline::crossStudio();

    expect($crossStudio)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CrossStudio::class);
});

test('facade can access customers account resource', function () {
    $customersAccount = Magicline::customersAccount();

    expect($customersAccount)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CustomersAccount::class);
});

test('facade can access customers communication resource', function () {
    $customersCommunication = Magicline::customersCommunication();

    expect($customersCommunication)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CustomersCommunication::class);
});

test('facade can access customers self service resource', function () {
    $customersSelfService = Magicline::customersSelfService();

    expect($customersSelfService)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CustomersSelfService::class);
});

test('facade can access devices resource', function () {
    $devices = Magicline::devices();

    expect($devices)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Devices::class);
});

test('facade can access employees resource', function () {
    $employees = Magicline::employees();

    expect($employees)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Employees::class);
});

test('facade can access finance resource', function () {
    $finance = Magicline::finance();

    expect($finance)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Finance::class);
});

test('facade can access memberships resource', function () {
    $memberships = Magicline::memberships();

    expect($memberships)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Memberships::class);
});

test('facade can access memberships self service resource', function () {
    $membershipsSelfService = Magicline::membershipsSelfService();

    expect($membershipsSelfService)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\MembershipsSelfService::class);
});

test('facade can access payments resource', function () {
    $payments = Magicline::payments();

    expect($payments)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Payments::class);
});

test('facade can access studios resource', function () {
    $studios = Magicline::studios();

    expect($studios)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Studios::class);
});

test('facade can access trial offers resource', function () {
    $trialOffers = Magicline::trialOffers();

    expect($trialOffers)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\TrialOffers::class);
});