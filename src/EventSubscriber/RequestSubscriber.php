<?php

namespace App\EventSubscriber;

use App\Domain\Logger\Model\Context;
use App\Domain\Logger\Model\Message;
use App\Service\Logger\AppLoger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    private const LOG_REQUEST = 'REQUEST';
    private const LOG_RESPONSE = 'RESPONSE';

    private AppLoger $logger;
    private TokenStorageInterface $tokenStorage;

    public function __construct(AppLoger $logger, TokenStorageInterface $tokenStorage)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
            ResponseEvent::class => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $context = new Context(self::class, $event, $this->getUsername());

        $this->logger->info(new Message(
            self::LOG_REQUEST,
            sprintf(
                '%s %s %s',
                $event->getRequest()->getProtocolVersion(),
                $event->getRequest()->getMethod(),
                $event->getRequest()->getUri(),
            ),
            $context
        ));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $context = new Context(self::class, $event, $this->getUsername());

        $this->logger->info(new Message(
            self::LOG_RESPONSE,
            sprintf(
                '%s %s %s (%s)',
                $event->getRequest()->getProtocolVersion(),
                $event->getRequest()->getMethod(),
                $event->getRequest()->getUri(),
                $event->getResponse()->getStatusCode(),
            ),
            $context
        ));
    }

    private function getUsername(): ?string
    {
        $token = $this->tokenStorage->getToken();
        $username = null;
        if (null !== $token) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                $username = $user->getUsername();
            }
        }

        return $username;
    }
}
