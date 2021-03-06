<?php

namespace Tests\NotificationChannels\ClickSend;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use NotificationChannels\ClickSend\ClickSendApi;
use NotificationChannels\ClickSend\ClickSendChannel;
use NotificationChannels\ClickSend\ClickSendMessage;
use NotificationChannels\ClickSend\Exceptions\CouldNotSendNotification;
use Verifiedit\ClicksendSms\SMS\SMS;

class ClickSendChannelTest extends MockeryTestCase
{
    private ClickSendApi $api;

    private ClickSendChannel $channel;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $app = new Container();
        $app->singleton('app', 'Illuminate\Container\Container');
        $app->singleton(
            'events',
            function ($app) {
                return new Dispatcher($app);
            }
        );
        $app->singleton(
            'config',
            function () {
                return new Repository(
                    [
                        'clicksend.enabled' => true,
                        'clicksend.driver' => 'clicksend',
                    ]
                );
            }
        );

        $api = Mockery::mock(SMS::class);
        $this->api = Mockery::mock(ClickSendApi::class, [$api, 'from', 'clicksend']);
        $this->channel = new ClickSendChannel($this->api, $app->make('events'));
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function testChannelCallsApi()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->shouldReceive('sendSms')
            ->once()
            ->withArgs(
                function ($arg) {
                    if ($arg instanceof ClickSendMessage) {
                        return true;
                    }
                    if (is_string($arg)) {
                        return true;
                    }

                    return false;
                }
            );

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function testDoesNotSendSmsWhenMissingRecipient()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->shouldReceive('sendSms')
            ->atMost()
            ->once()
            ->andThrow(CouldNotSendNotification::class);

        $this->channel->send(new TestNotifiableWithoutRouteNotificationFor(), new TestNotification());
    }

    public function testBadDriver()
    {
        $this->expectException(CouldNotSendNotification::class);

        Mockery::mock(ClickSendApi::class, [Mockery::mock(SMS::class), 'from', 'bad']);
    }

    public function testNotifiableWithAttribute()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->shouldReceive('sendSms')
            ->once()
            ->withArgs(
                function ($arg) {
                    if ($arg instanceof ClickSendMessage) {
                        return true;
                    }
                    if (is_string($arg)) {
                        return true;
                    }

                    return false;
                }
            );

        $this->channel->send(new TestNotifiableWithAttribute(), new TestNotification());
    }

    public function testNotifiableOnDemand()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->shouldReceive('sendSms')
            ->once()
            ->withArgs(
                function ($arg) {
                    if ($arg instanceof ClickSendMessage) {
                        return true;
                    }
                    if (is_string($arg)) {
                        return true;
                    }

                    return false;
                }
            );

        $notifiable = new AnonymousNotifiable();

        $this->channel->send($notifiable->route('clicksend', '+1234567890'), new TestNotification());
    }
}

class TestNotifiable
{
    public ?string $phone_number = null;

    public function routeNotificationFor(): string
    {
        return '+1234567890';
    }
}

class TestNotifiableWithoutRouteNotificationFor extends TestNotifiable
{
    public ?string $phone_number = null;

    public function routeNotificationFor(): string
    {
        return '';
    }
}

class TestNotifiableWithAttribute extends TestNotifiable
{
    public ?string $phone_number = '+1234567890';

    public function routeNotificationFor(): string
    {
        return '';
    }
}

class TestNotification extends Notification
{
    public function toClickSend(): ClickSendMessage
    {
        return new ClickSendMessage('message');
    }
}
