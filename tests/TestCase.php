<?php

namespace AlexBabintsev\Magicline\Tests;

use AlexBabintsev\Magicline\MagiclineServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'AlexBabintsev\\Magicline\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Ensure clean state after each test
        if (class_exists('Mockery')) {
            \Mockery::close();
        }
    }

    public function ignorePackageDiscoveriesFrom()
    {
        return [];
    }

    protected function getPackageProviders($app)
    {
        return [
            MagiclineServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('magicline.api_key', 'test-api-key');
        config()->set('magicline.base_url', 'https://test.magicline.com');
        config()->set('magicline.timeout', 30);
        config()->set('magicline.retry.times', 1);
        config()->set('magicline.retry.sleep', 100);
        config()->set('magicline.logging.enabled', false);

        // Disable exception handling that can cause issues in CI
        $app['config']->set('app.debug', true);
        $app['config']->set('app.env', 'testing');
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        // Additional CI compatibility
        $app['config']->set('session.driver', 'array');
        $app['config']->set('cache.default', 'array');
    }

    protected function resolveApplicationExceptionHandler($app)
    {
        // Override for CI compatibility - prevent HandleExceptions issues
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', function () {
            return new class {
                public function report(\Throwable $e) {}
                public function render($request, \Throwable $e) {}
                public function renderForConsole($output, \Throwable $e) {}
            };
        });
    }
}
