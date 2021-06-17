<?php

namespace Tests\NotificationChannels\ClickSend;

use NotificationChannels\ClickSend\ClickSendMessage;
use PHPUnit\Framework\TestCase;

class ClickSendMessageTest extends TestCase
{
    public function testCreateInstance()
    {
        $message = new ClickSendMessage('message');

        $this->assertEquals('message', $message->getContent());
    }
}
