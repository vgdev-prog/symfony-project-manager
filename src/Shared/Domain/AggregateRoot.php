<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use App\Shared\Domain\Contract\DomainEvent;

/**
 * Base class for all Aggregate Roots in the domain.
 *
 * Responsibilities:
 *   - Record domain events
 *   - Release events for dispatching
 *   - Ensure aggregate consistency
 *
 * Usage:
 * ```php
 * class User extends AggregateRoot
 * {
 *     public function signUp(Email $email, string $password): void
 *     {
 *         // Business logic
 *         $this->email = $email;
 *         $this->password = $password;
 *
 *         // Record domain event
 *         $this->recordEvent(new UserSignedUp($this->id, $email));
 *     }
 * }
 * ```
 */
abstract class AggregateRoot
{
    /**
     * @var DomainEvent[]
     */
    private array $domainEvents = [];

    /**
     * Record a domain event.
     *
     * Events are stored internally and released after saving.
     */
    protected function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * Release all recorded events.
     *
     * This is typically called after persisting the aggregate.
     *
     * @return DomainEvent[]
     */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    /**
     * Check if aggregate has pending events.
     */
    public function hasEvents(): bool
    {
        return !empty($this->domainEvents);
    }

    /**
     * Get pending events without releasing them.
     *
     * @return DomainEvent[]
     */
    public function getEvents(): array
    {
        return $this->domainEvents;
    }

    /**
     * Clear all pending events without dispatching.
     *
     * Use with caution - events will be lost!
     */
    public function clearEvents(): void
    {
        $this->domainEvents = [];
    }
}
