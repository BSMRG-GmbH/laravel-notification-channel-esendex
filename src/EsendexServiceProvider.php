<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\InvalidConfiguration;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

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
                $config = config('services.esendex');

                if (is_null($config)) {
                    throw InvalidConfiguration::configurationNotSet();
                }

                return new EsendexClient(new Client(), $config);
            });
    }
}
