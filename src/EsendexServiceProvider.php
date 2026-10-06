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
            ->needs(EsendexClientInterface::class)
            ->give(function () {
                $config = config('services.esendex');

                self::checkConfig($config);
                
                if(str_starts_with($config['account'], 'EXDE')) {
                    return new EsendexClientV2(new Client(), $config);
                }

                return new EsendexClient(new Client(), $config);
            });
    }

    protected static function checkConfig(?array $config) {
        if (!is_array($config) || empty($config)) {
            throw InvalidConfiguration::configurationNotSet();
        }

        if(!isset($config['account'])) {
            throw InvalidConfiguration::entryMissing('account');
        }

        if(!isset($config['api_key'])) {
            throw InvalidConfiguration::entryMissing('api_key');
        }
    }
}
