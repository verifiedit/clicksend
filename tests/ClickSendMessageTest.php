<?php

namespace Tests\NotificationChannels\ClickSend;

use NotificationChannels\ClickSend\ClickSendMessage;
use PHPUnit\Framework\TestCase;

class ClickSendMessageTest extends TestCase
{
    public function test_create_instance()
    {
        $message = new ClickSendMessage('message');

        $this->assertEquals('message', $message->getContent());
    }

    public function test_can_set_from_on_message()
    {
        $message = new ClickSendMessage('message');
        $message->setFrom('from');

        $this->assertEquals('from', $message->getFrom());
    }

    public function test_from_set_to_null_by_default()
    {
        $message = new ClickSendMessage('message');

        $this->assertNull($message->getFrom());
    }
}
