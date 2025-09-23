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

    expect($magicline->appointments())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Appointments::class)
        ->and($magicline->checkinVouchers())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CheckinVouchers::class)
        ->and($magicline->classes())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Classes::class)
        ->and($magicline->crossStudio())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CrossStudio::class)
        ->and($magicline->customers())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Customers::class)
        ->and($magicline->customersAccount())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CustomersAccount::class)
        ->and($magicline->customersCommunication())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CustomersCommunication::class)
        ->and($magicline->customersSelfService())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\CustomersSelfService::class)
        ->and($magicline->devices())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Devices::class)
        ->and($magicline->employees())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Employees::class)
        ->and($magicline->finance())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Finance::class)
        ->and($magicline->memberships())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Memberships::class)
        ->and($magicline->membershipsSelfService())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\MembershipsSelfService::class)
        ->and($magicline->payments())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Payments::class)
        ->and($magicline->studios())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\Studios::class)
        ->and($magicline->trialOffers())->toBeInstanceOf(\alexbabintsev\Magicline\Resources\TrialOffers::class);
});
