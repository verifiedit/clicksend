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

    public function testCanSetFromOnMessage()
    {
        $message = new ClickSendMessage('message');
        $message->setFrom('from');

        $this->assertEquals('from', $message->getFrom());
    }

    public function testFromSetToNullByDefault()
    {
        $message = new ClickSendMessage('message');

        $this->assertNull($message->getFrom());
    }
}
