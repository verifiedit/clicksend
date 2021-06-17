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

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return ClickSendMessage
     */
    public function setContent(string $content): ClickSendMessage
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom(string $from): void
    {
        $this->from = $from;
    }
}
