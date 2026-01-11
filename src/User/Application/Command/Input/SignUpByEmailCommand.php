<?php

declare(strict_types=1);

namespace App\User\Application\Command\Input;

final readonly class SignUpByEmailCommand
{
    public function __construct(
        public string $email,
        public string $password
    ){

    }
}
