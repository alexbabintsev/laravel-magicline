<?php

namespace alexbabintsev\Magicline\Tests\Unit;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Magicline;
use alexbabintsev\Magicline\Tests\TestCase;
use Illuminate\Http\Client\Factory;

class MagiclineServiceProviderTest extends TestCase
{
    public function test_magicline_client_is_registered_as_singleton()
    {
        $client1 = app(MagiclineClient::class);
        $client2 = app(MagiclineClient::class);

        expect($client1)->toBeInstanceOf(MagiclineClient::class);
        expect($client1)->toBe($client2);
    }

    public function test_magicline_is_registered_as_singleton()
    {
        $magicline1 = app(Magicline::class);
        $magicline2 = app(Magicline::class);

        expect($magicline1)->toBeInstanceOf(Magicline::class);
        expect($magicline1)->toBe($magicline2);
    }

    public function test_magicline_client_uses_config_values()
    {
        config()->set('magicline.api_key', 'test-api-key');
        config()->set('magicline.base_url', 'https://test.example.com');
        config()->set('magicline.timeout', 45);

        $client = app(MagiclineClient::class);

        expect($client)->toBeInstanceOf(MagiclineClient::class);
    }

    public function test_service_provider_registers_dependencies()
    {
        expect(app()->bound(MagiclineClient::class))->toBeTrue();
        expect(app()->bound(Magicline::class))->toBeTrue();
        expect(app()->bound(Factory::class))->toBeTrue();
    }
}
