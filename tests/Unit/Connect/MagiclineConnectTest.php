<?php

use AlexBabintsev\Magicline\Connect\Http\MagiclineConnectClient;
use AlexBabintsev\Magicline\Connect\MagiclineConnect;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineConnectClient::class);
    $this->connect = new MagiclineConnect($this->mockClient);
});

test('can access studios resource', function () {
    $resource = $this->connect->studios();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Studios::class);
});

test('can access campaigns resource', function () {
    $resource = $this->connect->campaigns();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Campaigns::class);
});

test('can access referrals resource', function () {
    $resource = $this->connect->referrals();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Referrals::class);
});

test('can access validation resource', function () {
    $resource = $this->connect->validation();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Validation::class);
});

test('can access address data resource', function () {
    $resource = $this->connect->addressData();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\AddressData::class);
});

test('can access leads resource', function () {
    $resource = $this->connect->leads();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Leads::class);
});

test('can access trial sessions resource', function () {
    $resource = $this->connect->trialSessions();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\TrialSessions::class);
});

test('can access rate bundles resource', function () {
    $resource = $this->connect->rateBundles();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\RateBundles::class);
});

test('can access contracts resource', function () {
    $resource = $this->connect->contracts();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\Contracts::class);
});

test('can access credit card tokenization resource', function () {
    $resource = $this->connect->creditCardTokenization();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\CreditCardTokenization::class);
});

test('can access image upload resource', function () {
    $resource = $this->connect->imageUpload();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\ImageUpload::class);
});

test('can access contract cancellation resource', function () {
    $resource = $this->connect->contractCancellation();

    expect($resource)->toBeInstanceOf(\AlexBabintsev\Magicline\Connect\Resources\ContractCancellation::class);
});

test('can get client', function () {
    $client = $this->connect->getClient();

    expect($client)->toBe($this->mockClient);
});
