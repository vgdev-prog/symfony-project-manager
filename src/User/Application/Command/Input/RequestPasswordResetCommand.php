<?php

declare(strict_types=1);

namespace App\User\Application\Command\Input;

final readonly class RequestPasswordResetCommand
{
    public function __construct(
        public string $email
    )
    {
    }

}
