<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('firebase', function () {
            return (new Factory)->withServiceAccount(config('firebase.credentials'))->create();
        });

        $this->app->singleton('firebase.messaging', function ($app) {
            return $app->make('firebase')->getMessaging();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
