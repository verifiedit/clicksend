<?php

namespace NotificationChannels\ClickSend\Exceptions;

use Exception;
use Throwable;
use Verifiedit\ClicksendSms\Exceptions\ClicksendApiException;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when content length is greater than 800 characters.
     *
     * @return static
     */
    public static function contentLengthLimitExceeded(): CouldNotSendNotification
    {
        return new static(
            'Notification was not sent. Content length may not be greater than 800 characters.'
        );
    }

    public static function missingRecipient(): CouldNotSendNotification
    {
        return static::notificationError('Missing recipient.');
    }

    /**
     * ClickSend returned an error message.
     */
    public static function clickSendErrorMessage(?string $message): CouldNotSendNotification
    {
        return static::notificationError($message ?? 'No message.');
    }

    /**
     * Thrown when message status is not SUCCESS.
     *
     * @return static
     */
    public static function clickSendApiException(ClicksendApiException $e): CouldNotSendNotification
    {
        return static::notificationError($e->getMessage());
    }

    public static function genericError(Throwable $e): CouldNotSendNotification
    {
        return new static(
            sprintf(
                'Generic Error: %s',
                $e->getMessage()
            )
        );
    }

    public static function notificationError(string $error): CouldNotSendNotification
    {
        return new static(
            sprintf(
                'Notification Error: %s',
                $error
            )
        );
    }

    public static function driverError(string $driver): CouldNotSendNotification
    {
        return new static(
            sprintf(
                'Invalid driver (%s)',
                $driver
            )
        );
    }
}
