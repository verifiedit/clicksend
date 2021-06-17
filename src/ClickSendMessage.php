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
}
