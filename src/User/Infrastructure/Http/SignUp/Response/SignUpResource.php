<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http\SignUp\Response;

use App\Shared\Domain\ValueObject\Id;
use App\User\Domain\Entity\User;

class SignUpResource
{
    public function __construct(
        public readonly string $id
    ) {
    }

    public static function make(Id $id): SignUpResource
    {
        return new self(
            $id->getValue()
        );

    }

}
