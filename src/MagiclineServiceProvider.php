<?php

namespace alexbabintsev\Magicline;

use alexbabintsev\Magicline\Commands\MagiclineCommand;
use alexbabintsev\Magicline\Http\MagiclineClient;
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
            ->hasMigration('create_magicline_table')
            ->hasCommand(MagiclineCommand::class);
    }

    public function packageRegistered(): void
    {
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
    }
}
