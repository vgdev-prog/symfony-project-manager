<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use DomainException;
use Override;
use Throwable;

/**
 * Base class for all domain exceptions with context.
 */
abstract class AbstractDomainException extends DomainException
{
    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    final public function getStatusCode(): int
    {
        return 400;
    }

    /**
     * Returns unique error code for API clients.
     */
    abstract public static function getDomainErrorCode(): string;

    /**
     * Returns public context safe to expose in API responses.
     *
     * Override this to expose specific fields to clients.
     * By default, returns empty array (no context exposed).
     */
    public function getPublicContext(): array
    {
        return [];
    }

    public static function getExamplePublicContext():array
    {
        return [];
    }

}
