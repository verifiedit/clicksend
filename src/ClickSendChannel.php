<?php

namespace NotificationChannels\ClickSend;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use NotificationChannels\ClickSend\Exceptions\CouldNotSendNotification;

class ClickSendChannel
{
    /**
     * @var ClickSendApi
     */
    protected $client;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * ClickSendChannel constructor.
     *
     * @param ClickSendApi $client
     * @param Dispatcher $events
     */
    public function __construct(ClickSendApi $client, Dispatcher $events)
    {
        $this->client = $client;
        $this->events = $events;
    }

    /**
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return array
     * @throws CouldNotSendNotification
     * @throws Exception
     */
    public function send($notifiable, Notification $notification): array
    {
        $to = $notifiable->routeNotificationForClickSend();
        $message = $this->getMessage($notifiable, $notification);

        $result = $this->client->sendSms($to, $message);

        if (empty($result['success'])) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, get_class($this), $result)
            );

            $message = Arr::get($result, 'message');

            // by throwing exception NotificationSent event is not triggered and we trigger NotificationFailed above instead
            throw CouldNotSendNotification::clickSendErrorMessage('Notification failed '.$message);
        }

        return $result;
    }

    /**
     * @param $notifiable
     * @param Notification $notification
     * @return ClickSendMessage
     * @throws Exception
     */
    public function getMessage($notifiable, Notification $notification): ClickSendMessage
    {
        if (!method_exists($notification, 'toClickSend')) {
            throw new Exception('The method toClickSend() does not exists on '.get_class($notification));
        }

        $message = $notification->toClickSend($notifiable);

        if (is_string($message)) {
            $message = new ClickSendMessage($message);
        }

        return $message;
    }
}
