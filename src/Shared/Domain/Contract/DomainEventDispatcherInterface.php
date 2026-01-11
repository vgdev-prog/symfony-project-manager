<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contract;

use App\Shared\Domain\AggregateRoot;

/**
 * Dispatches domain events from aggregates.
 *
 * This is a domain interface - implementations are in Infrastructure.
 */
interface DomainEventDispatcherInterface
{
    /**
     * Dispatch all events from an aggregate.
     *
     * Releases events from aggregate and dispatches them.
     */
    public function dispatch(AggregateRoot $aggregate): void;

    /**
     * Dispatch events from multiple aggregates.
     *
     * @param AggregateRoot[] $aggregates
     */
    public function dispatchAll(array $aggregates): void;
}
