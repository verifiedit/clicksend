<?php

namespace NotificationChannels\ClickSend;

class ClickSendMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $from;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): ClickSendMessage
    {
        $this->content = $content;

        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }
}
