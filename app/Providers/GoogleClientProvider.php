<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Google_Client;
use Illuminate\Support\ServiceProvider;

class GoogleClientProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Google_Client::class, function ($app) {
            $client = new Google_Client();
            $client->setAuthConfig(Storage::path('client_secret.json'));
            return $client;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
