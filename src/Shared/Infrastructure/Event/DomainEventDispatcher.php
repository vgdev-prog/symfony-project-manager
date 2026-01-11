<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Event;

use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\Contract\DomainEventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Symfony EventDispatcher implementation for domain events.
 *
 * Dispatches domain events and optionally logs them.
 */
final readonly class DomainEventDispatcher implements DomainEventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ?LoggerInterface $logger = null
    ) {}

    public function dispatch(AggregateRoot $aggregate): void
    {
        $events = $aggregate->releaseEvents();

        if (empty($events)) {
            return;
        }

        foreach ($events as $event) {
            $this->dispatchEvent($event);
        }
    }

    public function dispatchAll(array $aggregates): void
    {
        foreach ($aggregates as $aggregate) {
            $this->dispatch($aggregate);
        }
    }

    private function dispatchEvent(object $event): void
    {
        // Log event dispatch (optional, only in debug mode)
        if ($this->logger !== null) {
            $this->logger->debug('Dispatching domain event', [
                'event_class' => get_class($event),
                'event_data' => method_exists($event, 'toArray') ? $event->toArray() : [],
            ]);
        }

        // Dispatch to Symfony EventDispatcher
        $this->eventDispatcher->dispatch($event);
    }
}
