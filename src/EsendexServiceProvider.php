<?php

namespace Bsmrg\LaravelNotificationChannels\Esssendex;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\Messagebird\Exceptions\InvalidConfiguration;

class EsendexServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(EsendexChannel::class)
            ->needs(EsendexClient::class)
            ->give(function () {
                $config = config('services.essendex');

                if (is_null($config)) {
                    throw InvalidConfiguration::configurationNotSet();
                }

                return new EsendexClient(new Client(), $config);
            });
    }
}
