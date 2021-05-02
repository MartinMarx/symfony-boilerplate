<?php

namespace App\Domain\Logger\Model;

class Message
{
    private string $tag;
    private string $message;
    private ?Context $context;

    public function __construct(string $tag, string $message, ?Context $context = null)
    {
        $this->tag = $tag;
        $this->message = $message;
        $this->context = $context;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }
}
