<?php

namespace App\Providers;

use App\Mail\Transport\OAuthTransport;
use App\Services\PaymentService;
use App\Services\ServiceRegistrationService;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Psr\EventDispatcher\EventDispatcherInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(ServiceRegistrationService::class);
    }

    public function boot()
    {
        $this->app->afterResolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('oauth', function () {
                return new OAuthTransport(
                    config('mail.mailers.oauth.client_id'),
                    config('mail.mailers.oauth.client_secret'),
                    config('mail.mailers.oauth.refresh_token'),
                    config('mail.mailers.oauth.email'),
                );
            });
        });

        $this->app->bind(EventDispatcherInterface::class, function ($app) {
            return $app->make('events');
        });

        Paginator::useBootstrap();
    }
}