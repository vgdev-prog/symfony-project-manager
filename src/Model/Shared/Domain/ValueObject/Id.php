<?php

declare(strict_types=1);

namespace App\Model\Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

readonly class Id
{
    private function __construct(
        private string $id
    )
    {
    }

    public static function next(): Id
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue():string
    {
        return $this->id;
    }
}
