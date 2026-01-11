<?php

declare(strict_types=1);

namespace App\User\Application\Command\Input;

final readonly class SignUpByNetworkCommand
{

    public function __construct(
        public string $network,
        public string $identity
    )
    {
    }
}
