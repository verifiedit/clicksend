<?php

namespace Tests\NotificationChannels\ClickSend;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use NotificationChannels\ClickSend\ClickSendApi;
use NotificationChannels\ClickSend\ClickSendChannel;
use NotificationChannels\ClickSend\ClickSendMessage;
use NotificationChannels\ClickSend\Exceptions\CouldNotSendNotification;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\NativeType;
use PHPUnit\Framework\TestCase;
use Verifiedit\ClicksendSms\SMS\SMS;

class ClickSendChannelTest extends TestCase
{
    private ClickSendApi $api;

    private ClickSendChannel $channel;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
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

        $smsStub = $this->createStub(SMS::class);
        $this->api = $this->getMockBuilder(ClickSendApi::class)
            ->setConstructorArgs([$smsStub, 'from', 'clicksend'])
            ->onlyMethods(['sendSms'])
            ->getMock();
        $this->channel = new ClickSendChannel($this->api, $app->make('events'));
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function test_channel_calls_api()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->expects($this->once())
            ->method('sendSms')
            ->with(
                new IsType(NativeType::String),
                $this->callback(function ($arg) {
                    return $arg instanceof ClickSendMessage || is_string($arg);
                })
            )
            ->willThrowException(new CouldNotSendNotification());

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function test_does_not_send_sms_when_missing_recipient()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->expects($this->atMost(1))
            ->method('sendSms')
            ->willThrowException(new CouldNotSendNotification());

        $this->channel->send(new TestNotifiableWithoutRouteNotificationFor(), new TestNotification());
    }

    public function test_bad_driver()
    {
        $this->expectException(CouldNotSendNotification::class);

        // Add expectation for mock created in setUp, even though we don't use it
        $this->api->expects($this->never())->method('sendSms');

        $smsStub = $this->createStub(SMS::class);
        new ClickSendApi($smsStub, 'from', 'bad');
    }

    public function test_notifiable_with_attribute()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->expects($this->once())
            ->method('sendSms')
            ->with(
                new IsType(NativeType::String),
                $this->callback(
                    function ($arg) {
                        return $arg instanceof ClickSendMessage || is_string($arg);
                    }
                )
            )
            ->willThrowException(new CouldNotSendNotification());

        $this->channel->send(new TestNotifiableWithAttribute(), new TestNotification());
    }

    public function test_notifiable_on_demand()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->api->expects($this->once())
            ->method('sendSms')
            ->with(
                new IsType(NativeType::String),
                $this->callback(function ($arg) {
                    return $arg instanceof ClickSendMessage || is_string($arg);
                })
            )
            ->willThrowException(new CouldNotSendNotification());

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
