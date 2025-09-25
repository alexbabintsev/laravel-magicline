<?php

namespace AlexBabintsev\Magicline;

use AlexBabintsev\Magicline\Commands\MagiclineCommand;
use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Connect\Http\MagiclineConnectClient;
use AlexBabintsev\Magicline\Connect\MagiclineConnect;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MagiclineServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-magicline')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_magicline_logs_table')
            ->hasCommand(MagiclineCommand::class);
    }

    public function packageRegistered(): void
    {
        // Main Magicline API (with API key)
        $this->app->singleton(MagiclineClient::class, function ($app) {
            $config = $app['config']['magicline'];

            return new MagiclineClient(
                httpFactory: $app[Factory::class],
                apiKey: $config['api_key'],
                baseUrl: $config['base_url'],
                timeout: $config['timeout'],
                retryConfig: $config['retry'],
                loggingEnabled: $config['logging']['enabled'],
                logLevel: $config['logging']['level'],
                logger: Log::getFacadeRoot()
            );
        });

        $this->app->singleton(Magicline::class, function ($app) {
            return new Magicline($app[MagiclineClient::class]);
        });

        // Connect API (public API, no API key required)
        $this->app->singleton(MagiclineConnectClient::class, function ($app) {
            $config = $app['config']['magicline']['connect'];

            // Build base URL with tenant if specified
            $baseUrl = $config['base_url'];
            if ($config['tenant']) {
                $baseUrl = str_replace('connectdemo', $config['tenant'], $baseUrl);
            }

            return new MagiclineConnectClient(
                httpFactory: $app[Factory::class],
                baseUrl: $baseUrl,
                timeout: $config['timeout'],
                retryConfig: $config['retry'],
                loggingEnabled: $config['logging']['enabled'],
                logLevel: $config['logging']['level'],
                logger: Log::getFacadeRoot()
            );
        });

        $this->app->singleton(MagiclineConnect::class, function ($app) {
            return new MagiclineConnect($app[MagiclineConnectClient::class]);
        });
    }
}
