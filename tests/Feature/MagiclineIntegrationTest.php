<?php

use alexbabintsev\Magicline\Magicline;
use Illuminate\Support\Facades\Http;

test('magicline can be resolved from container', function () {
    $magicline = app(Magicline::class);

    expect($magicline)->toBeInstanceOf(Magicline::class);
});

test('magicline facade works', function () {
    Http::fake([
        'test.magicline.com/*' => Http::response(['data' => []], 200),
    ]);

    $magicline = app(Magicline::class);
    $customers = $magicline->customers();

    expect($customers)->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Customers::class);
});

test('all resources are accessible', function () {
    $magicline = app(Magicline::class);

    expect($magicline->appointments())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Appointments::class);
    expect($magicline->checkinVouchers())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CheckinVouchers::class);
    expect($magicline->classes())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Classes::class);
    expect($magicline->crossStudio())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CrossStudio::class);
    expect($magicline->customers())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Customers::class);
    expect($magicline->customersAccount())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CustomersAccount::class);
    expect($magicline->customersCommunication())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CustomersCommunication::class);
    expect($magicline->customersSelfService())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CustomersSelfService::class);
    expect($magicline->devices())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Devices::class);
    expect($magicline->employees())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Employees::class);
    expect($magicline->finance())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Finance::class);
    expect($magicline->memberships())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Memberships::class);
    expect($magicline->membershipsSelfService())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\MembershipsSelfService::class);
    expect($magicline->payments())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Payments::class);
    expect($magicline->studios())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Studios::class);
    expect($magicline->trialOffers())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\TrialOffers::class);
});
