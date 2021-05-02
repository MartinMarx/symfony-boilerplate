<?php

namespace App\Service\Logger;

use App\Domain\Logger\Model\Message;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class AppLoger
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function info(Message $message): void
    {
        $this->logger->log(LogLevel::INFO, $this->getMessage($message));
    }

    private function getMessage(Message $message): string
    {
        if (null === $message->getContext()) {
            return sprintf(
                '%-15s %s | No context',
                '['.strtoupper($message->getTag()).']',
                $message->getMessage(),
            );
        }

        return sprintf(
            '%-15s %s | %s | called by %s in %s',
            '['.strtoupper($message->getTag()).']',
            $message->getMessage(),
            $message->getContext()->getUser() ? $message->getContext()->getUser() : 'No user',
            $message->getContext()->getEvent()::class,
            $message->getContext()->getClass(),
        );
    }
}
