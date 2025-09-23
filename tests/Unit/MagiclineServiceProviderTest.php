<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Magicline;
use Illuminate\Http\Client\Factory;

test('magicline client is registered as singleton', function () {
    $client1 = app(MagiclineClient::class);
    $client2 = app(MagiclineClient::class);

    expect($client1)->toBeInstanceOf(MagiclineClient::class);
    expect($client1)->toBe($client2);
});

test('magicline is registered as singleton', function () {
    $magicline1 = app(Magicline::class);
    $magicline2 = app(Magicline::class);

    expect($magicline1)->toBeInstanceOf(Magicline::class);
    expect($magicline1)->toBe($magicline2);
});

test('magicline client uses config values', function () {
    config()->set('magicline.api_key', 'test-api-key');
    config()->set('magicline.base_url', 'https://test.example.com');
    config()->set('magicline.timeout', 45);

    $client = app(MagiclineClient::class);

    expect($client)->toBeInstanceOf(MagiclineClient::class);
});

test('service provider registers dependencies', function () {
    expect(app()->bound(MagiclineClient::class))->toBeTrue();
    expect(app()->bound(Magicline::class))->toBeTrue();
    expect(app()->bound(Factory::class))->toBeTrue();
});
