<?php

namespace NotificationChannels\ClickSend;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use NotificationChannels\ClickSend\Exceptions\CouldNotSendNotification;
use Throwable;
use Verifiedit\ClicksendSms\Exceptions\ClicksendApiException;
use Verifiedit\ClicksendSms\SMS\Message;
use Verifiedit\ClicksendSms\SMS\Messages;
use Verifiedit\ClicksendSms\SMS\RecipientAlreadySetException;
use Verifiedit\ClicksendSms\SMS\SMS;

/**
 * Click Send API using ClickSend API wrapper.
 *
 * @url https://github.com/ClickSend/clicksend-php
 */
class ClickSendApi
{
    public string $driver;

    private SMS $api;

    /**
     * @var string - default from config
     */
    protected string $smsFrom;

    /**
     * ClickSendApi constructor.
     *
     * @throws CouldNotSendNotification
     */
    public function __construct(SMS $api, string $smsFrom, string $driver)
    {
        $this->api = $api;
        $this->smsFrom = $smsFrom;

        if ($driver !== 'clicksend' && $driver !== 'log') {
            throw CouldNotSendNotification::driverError($driver);
        }

        $this->driver = $driver;
    }

    /**
     * @throws CouldNotSendNotification
     * @throws RecipientAlreadySetException
     */
    #[ArrayShape(['success' => 'bool', 'message' => 'string', 'data' => 'array'])]
    public function sendSms(string $to, ClickSendMessage $message): array
    {
        $from = $message->getFrom() ?? $this->smsFrom;

        $data = [
            'from' => $from,
            'to' => $to,
            'body' => $message->getContent(),
        ];

        $result = [
            'success' => false,
            'message' => '',
            'data' => $data,
        ];

        $messages = new Messages();
        $messages->add((new Message($message->getContent()))->setTo($to)->setFrom($from));

        if ($this->driver === 'log') {
            Log::debug(
                'ClickSend SMS',
                [
                    'data' => $data,
                    'payload' => $messages->toArray(),
                ]
            );
            $result['success'] = true;
            $result['message'] = 'Message sent successfully.';

            return $result;
        }

        try {
            $response = $this->api->send($messages);

            $data = json_decode((string) $response->getBody(), true);

            if ($data['response_code'] != 'SUCCESS') {
                // communication error
                throw CouldNotSendNotification::clickSendErrorMessage($data['response_msg']);
            } elseif (Arr::get($data, 'data.messages.0.status') != 'SUCCESS') {
                // sending error
                throw CouldNotSendNotification::clickSendErrorMessage(Arr::get($data, 'data.messages.0.status'));
            } else {
                $result['success'] = true;
                $result['message'] = 'Message sent successfully.';
            }
        } catch (ClicksendApiException $e) {
            throw CouldNotSendNotification::clickSendApiException($e);
        } catch (Throwable $e) {
            throw CouldNotSendNotification::genericError($e);
        }

        return $result;
    }
}
