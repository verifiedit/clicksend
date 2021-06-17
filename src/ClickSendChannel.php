<?php

namespace NotificationChannels\ClickSend;

use Exception;
use Illuminate\Contracts\Config\Repository;
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
     * @var string
     */
    public $prefix;

    /**
     * ClickSendChannel constructor.
     *
     * @param ClickSendApi $client
     * @param Dispatcher $events
     * @param Repository $config
     */
    public function __construct(ClickSendApi $client, Dispatcher $events, Repository $config)
    {
        $this->client = $client;
        $this->events = $events;
        $this->enabled = $config['clicksend.enabled'];
        $this->prefix = $config['clicksend.prefix'];
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
        if (!$this->enabled) {
            return [];
        }

        $to = (isset($notifiable->routes) && isset($notifiable->routes['notification_for_click_send'])) ?
            $notifiable->routes['notification_for_click_send'] :
            $notifiable->routeNotificationForClicksend();

        if (!$to) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $to = $this->checkPrefix($to);

        $message = $this->getMessage($notifiable, $notification);

        $message = new ClickSendMessage($to, $message);

        $message = $this->updateClickSendMessage($message, $notification);

        try {
            $result = $this->client->sendSms($message);

            if (empty($result['success']) || !$result['success']) {
                $this->events->dispatch(
                    new NotificationFailed($notifiable, $notification, get_class($this), $result)
                );

                $message = Arr::get($result, 'message');

                // by throwing exception NotificationSent event is not triggered and we trigger NotificationFailed above instead
                throw CouldNotSendNotification::clickSendErrorMessage($message);
            }

            return $result;
        } catch (Exceptions\CouldNotSendNotification $e) {
            $this->events->dispatch(
                new NotificationFailed(
                    $notifiable,
                    $notification,
                    get_class($this),
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'data' => [],
                    ]
                )
            );

            // by throwing exception NotificationSent event is not triggered and we trigger NotificationFailed above instead
            throw $e;
        }
    }

    /**
     * @param $notifiable
     * @param Notification $notification
     * @return string
     * @throws Exception
     */
    public function getMessage($notifiable, Notification $notification): string
    {
        if (! method_exists($notification, 'getMessage')) {
            throw new Exception('The method getMessage() does not exists on '.get_class($notification));
        }

        $message = $notification->getMessage($notifiable);

        if (! is_string($message)) {
            throw new Exception('getMessage() on '.get_class($notification).' did not return string');
        }

        return $message;
    }

    /**
     * @param ClickSendMessage $message
     * @param Notification $notification
     * @return ClickSendMessage
     */
    public function updateClickSendMessage(ClickSendMessage $message, Notification $notification): ClickSendMessage
    {
        if (! method_exists($notification, 'updateClickSendMessage')) {
            return $message;
        }

        return $notification->updateClickSendMessage($message);
    }

    /**
     * @param string $to
     * @return string
     */
    public function checkPrefix(string $to): string
    {
        if (! empty($this->prefix)) {
            if (substr($to, 0, strlen($this->prefix)) !== $this->prefix) {
                return $this->prefix.$to;
            }
        }

        return $to;
    }
}
