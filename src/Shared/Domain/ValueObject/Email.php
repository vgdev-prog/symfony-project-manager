<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidEmailException;
use InvalidArgumentException;

/**
 * Email Value Object - represents a valid email address.
 *
 * Ensures email is always in lowercase and valid format.
 */
final readonly class Email
{
    private string $value;

    private function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }

        $this->value = mb_strtolower($email);
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
