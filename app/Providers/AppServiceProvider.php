<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ApiClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ApiClient::class, function ($app) {
            return new ApiClient('http://localhost:8000/api');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
