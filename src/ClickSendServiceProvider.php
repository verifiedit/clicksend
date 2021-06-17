<?php

namespace NotificationChannels\ClickSend;

use ClickSend\Api\SMSApi;
use ClickSend\Configuration;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ClickSendServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__.'/../config/clicksend.php' => config_path('clicksend.php'),
            ],
            'config'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/clicksend.php',
            'clicksend'
        );
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(SMSApi::class, function () {
            $configuration = Configuration::getDefaultConfiguration()
                ->setUsername($this->app['config']['clicksend.username'])
                ->setPassword($this->app['config']['clicksend.apikey']);

            return new SMSApi(new Client(), $configuration);
        });

        $this->app->singleton(ClickSendApi::class, function (Application $app) {
            return new ClickSendApi($app->make(SMSApi::class), $this->app['config']['clicksend.sms-from'], $this->app['config']['clicksend.driver']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [ClickSendApi::class];
    }
}