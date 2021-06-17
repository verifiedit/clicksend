<?php

return [
    /**
     * clicksend or log.
     */
    'driver' => env('CLICKSEND_DRIVER', 'clicksend'),

    /*
     * set to false to disable.
     */
    'enabled'   => env('CLICKSEND_ENABLED', true),

    /**
     * ClickSend username.
     */
    'username' => env('CLICKSEND_USERNAME', ''),

    /**
     * ClickSend API Key.
     */
    'apikey'   => env('CLICKSEND_APIKEY', ''),

    /**
     * ClickSend Send From.
     */
    'sms-from'  => env('CLICKSEND_SMS_FROM', ''),

    /**
     * ClickSend enforced prefix
     * For example +1
     * This should only be used if you are absolutely sure every `to` will need this prefix.
     */
    'prefix'  => env('CLICKSEND_PREFIX', ''),
];
