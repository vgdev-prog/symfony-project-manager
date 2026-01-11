<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid;

readonly class Id
{
    private function __construct(
        private string $id
    )
    {
        if (!Uuid::isValid($this->id)) {
            throw new InvalidUuidException($id);
        }
    }

    public static function next(): Id
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue():string
    {
        return $this->id;
    }

    public static function fromString(string $value): Id
    {
        return new self($value);
    }
    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
     return $this->id;
    }
}
