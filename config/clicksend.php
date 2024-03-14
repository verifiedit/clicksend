<?php

return [
    /**
     * clicksend or log.
     */
    'driver' => env('CLICKSEND_DRIVER', 'clicksend'),

    /*
     * set to false to disable.
     */
    'enabled' => env('CLICKSEND_ENABLED', true),

    /**
     * ClickSend username.
     */
    'username' => env('CLICKSEND_USERNAME', ''),

    /**
     * ClickSend API Key.
     */
    'apikey' => env('CLICKSEND_APIKEY', ''),

    /**
     * ClickSend Send From.
     */
    'from' => env('CLICKSEND_SMS_FROM', ''),
];
