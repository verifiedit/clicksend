<?php

namespace NotificationChannels\ClickSend;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Verifiedit\ClicksendSms\ClicksendClient;
use Verifiedit\ClicksendSms\SMS\SMS;

class ClickSendServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @throws Exception
     */
    public function boot()
    {
        if (! function_exists('config_path')) {
            throw new Exception('Please install in a Laravel project to use this package.');
        }

        $this->publishes(
            [
                __DIR__.'/../config/clicksend.php' => config_path('clicksend.php'),
            ],
            'clicksend-config'
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
        $this->app->singleton(SMS::class, function () {
            $client = ClicksendClient::make(
                $this->app['config']['clicksend.username'],
                $this->app['config']['clicksend.apikey']
            );

            return new SMS($client);
        });

        $this->app->singleton(ClickSendApi::class, function (Application $app) {
            return new ClickSendApi(
                $app->make(SMS::class),
                $this->app['config']['clicksend.from'],
                $this->app['config']['clicksend.driver']
            );
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
