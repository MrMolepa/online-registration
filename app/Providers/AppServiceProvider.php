<?php

namespace App\Providers;

use App\Mail\Transport\OAuthTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Psr\EventDispatcher\EventDispatcherInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->afterResolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('oauth', function () {
                $config = config('mail.mailers.oauth');
                return new  OAuthTransport(
                    config('mail.mailers.oauth.client_id'),
                    config('mail.mailers.oauth.client_secret'),
                    config('mail.mailers.oauth.refresh_token'),
                    config('mail.mailers.oauth.email'),

                );
            });
        });

        // Ensure EventDispatcher is properly bound
        $this->app->bind(EventDispatcherInterface::class, function ($app) {
            return $app->make('events');
        });


        Paginator::useBootstrap();
    }
}
