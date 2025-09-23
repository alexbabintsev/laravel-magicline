<?php

use AlexBabintsev\Magicline\Magicline;
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

    expect($customers)->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Customers::class);
});

test('all resources are accessible', function () {
    $magicline = app(Magicline::class);

    expect($magicline->appointments())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Appointments::class)
        ->and($magicline->checkinVouchers())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CheckinVouchers::class)
        ->and($magicline->classes())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Classes::class)
        ->and($magicline->crossStudio())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CrossStudio::class)
        ->and($magicline->customers())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Customers::class)
        ->and($magicline->customersAccount())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CustomersAccount::class)
        ->and($magicline->customersCommunication())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CustomersCommunication::class)
        ->and($magicline->customersSelfService())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\CustomersSelfService::class)
        ->and($magicline->devices())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Devices::class)
        ->and($magicline->employees())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Employees::class)
        ->and($magicline->finance())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Finance::class)
        ->and($magicline->memberships())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Memberships::class)
        ->and($magicline->membershipsSelfService())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\MembershipsSelfService::class)
        ->and($magicline->payments())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Payments::class)
        ->and($magicline->studios())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\Studios::class)
        ->and($magicline->trialOffers())->toBeInstanceOf(\AlexBabintsev\Magicline\Resources\TrialOffers::class);
});
