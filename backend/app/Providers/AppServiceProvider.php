<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;
use App\Events\OrderCreated;
use App\Events\OrderCompleted;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\SendOrderCompletedEmail;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Fix SSL certificate error on Windows local development
        if (app()->environment('local')) {
            $this->app->bind(\GuzzleHttp\Client::class, function () {
                return new Client(['verify' => false]);
            });

            // Override HTTP client cho Socialite
            \Laravel\Socialite\Facades\Socialite::extend('google', function ($app) {
                $config = $app['config']['services.google'];
                return \Laravel\Socialite\Facades\Socialite::buildProvider(
                    \Laravel\Socialite\Two\GoogleProvider::class,
                    $config
                )->setHttpClient(new Client(['verify' => false]));
            });

            \Laravel\Socialite\Facades\Socialite::extend('facebook', function ($app) {
                $config = $app['config']['services.facebook'];
                return \Laravel\Socialite\Facades\Socialite::buildProvider(
                    \Laravel\Socialite\Two\FacebookProvider::class,
                    $config
                )->setHttpClient(new Client(['verify' => false]));
            });
        }

        // Event → Listener bindings
        Event::listen(OrderCreated::class, SendOrderConfirmationEmail::class);
        Event::listen(OrderCompleted::class, SendOrderCompletedEmail::class);
    }
}
