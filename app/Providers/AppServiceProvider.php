<?php

declare(strict_types=1);

namespace App\Providers;

use App\Socialite\AzureAdProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Socialite::extend('azure', function (Application $app) {
            $config = $app['config']['services.azure_ad'];

            return new AzureAdProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect'],
                $config['tenant'] ?? 'common',
            );
        });
    }
}
