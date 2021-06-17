<?php

namespace Tests\NotificationChannels\ClickSend;

use NotificationChannels\ClickSend\ClickSendMessage;
use PHPUnit\Framework\TestCase;

class ClickSendMessageTest extends TestCase
{
    public function testCreateInstance()
    {
        $message = new ClickSendMessage('to', 'message', 'from');

        $this->assertEquals('to', $message->getTo());
        $this->assertEquals('message', $message->getContent());
        $this->assertEquals('from', $message->getFrom());
    }

    public function testFromArgumentIsOptional()
    {
        $message = new ClickSendMessage('to', 'message');

        $this->assertEquals('to', $message->getTo());
        $this->assertEquals('message', $message->getContent());
        $this->assertNull($message->getFrom());
    }

    public function testFromSetter()
    {
        $message = new ClickSendMessage('to', 'message');
        $message->setFrom('from');

        $this->assertEquals('to', $message->getTo());
        $this->assertEquals('message', $message->getContent());
        $this->assertEquals('from', $message->getFrom());
    }

    public function testOverrideSetters()
    {
        $message = new ClickSendMessage('to', 'message');
        $message
            ->setTo('two')
            ->setContent('some message')
            ->setFrom('from');

        $this->assertEquals('two', $message->getTo());
        $this->assertEquals('some message', $message->getContent());
        $this->assertEquals('from', $message->getFrom());
    }
}
