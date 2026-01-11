<?php

declare(strict_types=1);

namespace App\User\Application\Command\Input;

final readonly class ConfirmSignUpCommand
{
    public function __construct(
        public string $token
    )
    {
    }

}
