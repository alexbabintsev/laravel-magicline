<?php

use AlexBabintsev\Magicline\Connect\MagiclineConnect;
use AlexBabintsev\Magicline\Facades\MagiclineConnect as MagiclineConnectFacade;

test('magicline connect can be resolved from container', function () {
    $connect = $this->app->make(MagiclineConnect::class);

    expect($connect)->toBeInstanceOf(MagiclineConnect::class);
});

test('magicline connect facade works', function () {
    $connect = MagiclineConnectFacade::getFacadeRoot();

    expect($connect)->toBeInstanceOf(MagiclineConnect::class);
});

test('all connect resources are accessible', function () {
    $connect = $this->app->make(MagiclineConnect::class);

    expect($connect->studios())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Studios::class);
    expect($connect->campaigns())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Campaigns::class);
    expect($connect->referrals())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Referrals::class);
    expect($connect->validation())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Validation::class);
    expect($connect->addressData())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\AddressData::class);
    expect($connect->leads())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Leads::class);
    expect($connect->trialSessions())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\TrialSessions::class);
    expect($connect->rateBundles())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\RateBundles::class);
    expect($connect->contracts())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Contracts::class);
    expect($connect->creditCardTokenization())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\CreditCardTokenization::class);
    expect($connect->imageUpload())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\ImageUpload::class);
    expect($connect->contractCancellation())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\ContractCancellation::class);
});

test('connect facade can access resources', function () {
    expect(MagiclineConnectFacade::studios())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Studios::class);
    expect(MagiclineConnectFacade::campaigns())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Campaigns::class);
    expect(MagiclineConnectFacade::leads())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Leads::class);
    expect(MagiclineConnectFacade::trialSessions())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\TrialSessions::class);
    expect(MagiclineConnectFacade::contracts())->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Contracts::class);
});
