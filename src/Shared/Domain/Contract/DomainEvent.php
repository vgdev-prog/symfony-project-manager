<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contract;

use DateTimeImmutable;

/**
 * Base interface for all domain events.
 *
 * Domain events represent something that happened in the domain
 * that other parts of the system might be interested in.
 *
 * Examples:
 *   - UserSignedUp
 *   - OrderPlaced
 *   - PaymentProcessed
 */
interface DomainEvent
{
    /**
     * When the event occurred.
     */
    public function occurredOn(): DateTimeImmutable;

    /**
     * Aggregate ID that emitted the event.
     */
    public function aggregateId(): string;

    /**
     * Event payload for serialization.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
