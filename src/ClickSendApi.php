<?php

namespace NotificationChannels\ClickSend;

use ClickSend\Api\SMSApi;
use ClickSend\ApiException;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use NotificationChannels\ClickSend\Exceptions\CouldNotSendNotification;
use Throwable;

/**
 * Click Send API using ClickSend API wrapper.
 *
 * @url https://github.com/ClickSend/clicksend-php
 */
class ClickSendApi
{
    /**
     * @var string
     */
    public $driver;

    /**
     * @var SMSApi
     */
    private $api;

    /**
     * @var string - default from config
     */
    protected $smsFrom;

    /**
     * ClickSendApi constructor.
     *
     * @param SMSApi $api
     * @param        $smsFrom
     * @param $driver
     * @throws CouldNotSendNotification
     */
    public function __construct(SMSApi $api, $smsFrom, $driver)
    {
        $this->api = $api;
        $this->smsFrom = $smsFrom;

        if ($driver !== 'clicksend' && $driver !== 'log') {
            throw CouldNotSendNotification::driverError($driver);
        }

        $this->driver = $driver;
    }

    /**
     * @param ClickSendMessage $message
     * @return array
     * @throws CouldNotSendNotification
     */
    public function sendSms(ClickSendMessage $message): array
    {
        $data = [
            'from' => $message->getFrom() ?? $this->smsFrom,
            'to' => $message->getTo(),
            'body' => $message->getContent(),
        ];

        $result = [
            'success' => false,
            'message' => '',
            'data' => $data,
        ];

        $payload = new SmsMessageCollection(['messages' => [new SmsMessage($data)]]);

        if ($this->driver === 'log') {
            Log::debug(
                'ClickSend SMS',
                [
                    'data' => $data,
                    'payload' => $payload,
                ]
            );
            $result['success'] = true;
            $result['message'] = 'Message sent successfully.';

            return $result;
        }

        try {
            $response = json_decode($this->api->smsSendPost($payload), true);

            if ($response['response_code'] != 'SUCCESS') {
                // communication error
                throw CouldNotSendNotification::clickSendErrorMessage($response['response_msg']);
            } elseif (Arr::get($response, 'data.messages.0.status') != 'SUCCESS') {
                // sending error
                throw CouldNotSendNotification::clickSendErrorMessage(Arr::get($response, 'data.messages.0.status'));
            } else {
                $result['success'] = true;
                $result['message'] = 'Message sent successfully.';
            }
        } catch (APIException $e) {
            throw CouldNotSendNotification::clickSendApiException($e);
        } catch (Throwable $e) {
            throw CouldNotSendNotification::genericError($e);
        }

        return $result;
    }


    /**
     * Return Client for accessing all other api functions.
     *
     * @return SMSApi
     */
    public function getClient(): SMSApi
    {
        return $this->api;
    }
}
