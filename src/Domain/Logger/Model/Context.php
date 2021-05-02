<?php

namespace App\Domain\Logger\Model;

class Context
{
    private string $class;
    private object $event;
    private ?string $user;

    public function __construct(string $class, object $event, ?string $user = null)
    {
        $this->class = $class;
        $this->event = $event;
        $this->user = $user;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getEvent(): object
    {
        return $this->event;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }
}
