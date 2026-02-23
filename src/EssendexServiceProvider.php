<?php

namespace Bsmrg\LaravelNotificationChannels\Essendex;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\Messagebird\Exceptions\InvalidConfiguration;

class EssendexServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(EssendexChannel::class)
            ->needs(EssendexClient::class)
            ->give(function () {
                $config = config('services.essendex');

                if (is_null($config)) {
                    throw InvalidConfiguration::configurationNotSet();
                }

                return new EssendexClient(new Client(), $config);
            });
    }
}
