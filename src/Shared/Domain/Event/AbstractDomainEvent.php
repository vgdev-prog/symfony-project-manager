<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Shared\Domain\Contract\DomainEvent;
use DateTimeImmutable;

/**
 * Abstract base class for domain events.
 *
 * Provides common implementation for occurredOn timestamp.
 */
abstract class AbstractDomainEvent implements DomainEvent
{
    private DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->occurredOn = new DateTimeImmutable();
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
