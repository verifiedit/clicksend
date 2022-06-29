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

    /**
     * @return CouldNotSendNotification
     */
    public static function missingRecipient(): CouldNotSendNotification
    {
        return static::notificationError('Missing recipient.');
    }

    /**
     * ClickSend returned an error message.
     *
     * @param string|null $message
     *
     * @return CouldNotSendNotification
     */
    public static function clickSendErrorMessage(?string $message): CouldNotSendNotification
    {
        return static::notificationError($message ?? 'No message.');
    }

    /**
     * Thrown when message status is not SUCCESS.
     *
     * @param ClicksendApiException $e
     *
     * @return static
     */
    public static function clickSendApiException(ClicksendApiException $e): CouldNotSendNotification
    {
        return static::notificationError($e->getMessage());
    }

    /**
     * @param Throwable $e
     *
     * @return CouldNotSendNotification
     */
    public static function genericError(Throwable $e): CouldNotSendNotification
    {
        return new static(
            sprintf(
                'Generic Error: %s',
                $e->getMessage()
            )
        );
    }

    /**
     * @param string $error
     *
     * @return CouldNotSendNotification
     */
    public static function notificationError(string $error): CouldNotSendNotification
    {
        return new static(
            sprintf(
                'Notification Error: %s',
                $error
            )
        );
    }

    /**
     * @param string $driver
     * @return CouldNotSendNotification
     */
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
